<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Library\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{
    
    function index(Request $request){
        $user = Auth::user();
        if(!$user) return Response::error(401)->json();
        
        $user = User::with(['pages', 'pages.links' => function($query){
            $query->orderBy('position', 'desc');
        }, 'pages.visits' => function($query){
            $query->latest();
        }, 'pages.links.clicks' => function($query){
            $query->latest();
        }])->find($user->unique_id);

        return Response::success()->json([
            'user' => $user
        ]);
    }

    function update(UpdateUserRequest $request){
        $user = Auth::user();
        User::find($user->unique_id)->update($request->safe()->all());
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
