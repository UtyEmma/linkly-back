<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Library\Base64;
use App\Library\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller{
    
    function index(Request $request){
        $user = Auth::user();
        if(!$user) return Response::error(401)->json();
        $user = User::relations()->find($user->unique_id);
        return Response::success()->json([
            'user' => $user
        ]);
    }

    function update(UpdateUserRequest $request, Base64 $base64){
        $user = Auth::user();
        $image = $request->filled("avatar") ? $base64->parse($request->avatar)->check()->type()->upload(public_path('/images/storage')) : $user->avatar;
        User::find($user->unique_id)->update($request->safe()->merge([
            'avatar' => $image
        ])->all());
        $user = User::relations()->find($user->unique_id);
        return Response::success()->json([
            'user' => $user
        ]);
    }

    function updatePassword(UpdatePasswordRequest $request, Base64 $base64){
        $user = Auth::user();
        if(!Hash::check($request->current_password, $user->password)){
            return Response::error(422)->json([
                'errors' => [
                    'current_password' => 'Current Password is incorrect' 
                ]
            ]);
        }
        
        User::find($user->unique_id)->update([
            'password' => Hash::make($user->password)
        ]);

        $user = User::relations()->find($user->unique_id);
        return Response::success()->json([
            'user' => $user
        ]);
    }

    function destroy(Request $request){
        $user = Auth::user();
        $request->force ? User::find($user->unique_id)->forceDelete() : User::destroy($user->unique_id);
        return Response::success()->json("Account Deleted");
    }


}
