<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\RecoverPasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RememberUserRequest;
use App\Library\Response;
use App\Library\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller {

    function register(RegisterRequest $request){
        $unique_id = Token::unique('users');
        
        $user = User::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'password' => Hash::make($request->password)
        ])->toArray());

        $user = User::with(['pages', 'pages.links'])->find($user->unique_id);
        $token = $user->createToken('auth')->plainTextToken;

        return Response::success()->json("Registration Successful", [
            'token' => $token,
            'user' => $user
        ]);
    }

    function login(LoginRequest $request){
        if(!Auth::attempt($request->only(['email', 'password']))) return Response::error(400)->json('Invalid Email or Password');
        $user = User::where($request->only('email'))->with(['pages', 'pages.links'])->first();
        $token = $user->createToken('auth')->plainTextToken;

        if($request->remember) {
            $user->remember_token = Token::unique('users', 'remember_token');
            $user->save();
        }

        return Response::success()->json("Welcome Back", [
            'token' => $token,
            'user' => $user,
            'remember_token' => $user->remember_token
        ]);
    }

    function rememberUser(RememberUserRequest $request){
        $user = User::where('remember_token', $request->token)->with(['pages', 'pages.links'])->first();
        $token = $user->createToken('auth')->plainTextToken;

        $user->remember_token = Token::unique('users', 'remember_token');
        $user->save();
        
        return Response::success()->json("Welcome Back", [
            'token' => $token,
            'user' => $user,
            'remember_token' => $user->remember_token
        ]);
    }

    function recoverPassword(RecoverPasswordRequest $request){
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT 
                ? Response::success()->json("Password Reset Link Sent") 
                : Response::error()->json("Password Reset Link could not be sent");
    }

    function resetPassword(PasswordResetRequest $request){
        $status = Password::reset(
            $request->only('email', 'token', 'password'),
            function($user) use($request){
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ]);
            }
        );

        return $status == Password::PASSWORD_RESET 
                        ? Response::success()->json("Password Changed")
                        : Response::error(500)->json();
    }

}
