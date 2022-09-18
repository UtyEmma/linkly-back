<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Services\SessionService;
use App\Library\Base64;
use App\Library\FileHandler;
use App\Library\Response;
use App\Library\Token;
use App\Models\Page;
use App\Models\Sessions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller {
    
    function create(CreatePageRequest $request){
        $unique_id = Token::unique('pages');
        $user = auth()->user();

        $slug = $request->slug ?? Str::slug($request->title);

        $page = Page::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'user_id' => $user->unique_id,
            'slug' => $slug,
            'logo' => null
        ])->toArray());
        
        return Response::success()->json('Page Created', [
            'page' => $page
        ]);
    }

    function list($user_id = null){
        $user = $user_id ? User::find($user_id) : Auth::user();
        
        $pages = $user->pages()->with(['links' => function($query){
            $query->orderBy('position', 'desc');
        }, 'visits' => function($query){
            $query->latest();
        }, 'links.clicks' => function($query){
            $query->latest();
        }])->withCount(['clicks', 'visits'])->get();

        return Response::success()->json([
            'pages' => $pages
        ]);
    }


    function details(Request $request, $slug){
        $user = $request->user();

        $page = Page::where([
            'slug' => $slug,
            'user_id' => $user->unique_id
        ])->with(['links' => function($query){
            $query->orderBy('position', 'desc');
        }, 'visits' => function($query){
            $query->latest();
        }, 'links.clicks' => function($query){
            $query->latest();
        }, 'clicks'])->withCount(['clicks', 'visits'])->first();

        if(!$page) return Response::error(404)->json("Page does not Exist");

        return Response::success()->json([
            'page' => $page
        ]);
    }
    
    function show(Request $request, SessionService $session, $slug){
        if(!$page = Page::where('slug', $slug)->with(['links' => function($query){
            $query->where('status', 'published')->orderBy('position', 'desc');
        }])->first()) return Response::error(404)->json("Page does not exist");

        $session = $session->fetchOrCreate($page, $request->session); 
        
        return Response::success()->json([
            'page' => $page,
            'session' => $session
        ]);
    }
    
    function update(UpdatePageRequest $request, Base64 $base64, $page_id){
        if(!$page = Page::with(['links' => function($query){
            $query->orderBy('position', 'desc');
        }, 'visits' => function($query){
            $query->latest();
        }, 'links.clicks' => function($query){
            $query->latest();
        }])->find($page_id)) return Response::error(404)->json("Page does not Exist");
        
        $image = $request->filled("logo") ? $base64->parse($request->logo)->check()->type()->upload(public_path('/images/storage')) : $page->logo;
        $page->update($request->safe()->merge([
            'logo' => $image
        ])->all());
        $page->refresh();

        return Response::success()->json([
            'page' => $page
        ]);
    }

    function destroy($page_id){
        if(!$page = Page::find($page_id)) return Response::error(404)->json("Page does not Exist");
        $page->delete();

        $pages = Page::with(['links', 'visits', 'links.clicks']);
        return Response::success()->json([
            'pages' => $pages
        ]);
    }



} 