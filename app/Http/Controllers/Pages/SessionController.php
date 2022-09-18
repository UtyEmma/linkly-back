<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Library\Response;
use App\Library\Token;
use App\Models\Page;
use App\Models\Sessions;
use App\Models\Views;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class SessionController extends Controller {
    

    function create(Request $request, $session_id){

        if(
            $request->filled('session') && 
            $session = Sessions::find($request->session)
        ) return $this->registerView($request, $session);

        $unique_id = Token::unique('sessions');

        $page = Page::find($request->page_id);
        $location = Location::get($request->ip());

        $session = Sessions::create([
            'page_id' => $page->unique_id,
            'unique_id' => $unique_id,
            'ip_address' => $request->ip(),
            'country' => $location->countryName,
            'country_code' =>  $location->countryCode,
            'city' =>  $location->regionName,
            'device' => $request->device,
            'referrer' => $request->referrer
        ]);

        $this->createView($session);

        return Response::success()->json([
            'session' => $unique_id 
        ]);
    }

    function registerView(Request $request, Sessions $session){   
        $this->createView($session);     
        return Response::success()->json([
            'session' => $session->unique_id
        ]);
    }

    function createView($session){
        $unique_id = Token::unique('views');
        Views::create([
            'unique_id' => $unique_id,
            'session_id' => $session->unique_id,
            'page_id' => $session->page_id
        ]);
    }

}
