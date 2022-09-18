<?php

namespace App\Http\Services;

use App\Library\Token;
use App\Models\Page;
use App\Models\Sessions;
use App\Models\Views;
use Stevebauman\Location\Facades\Location;

class SessionService {
    function create($page){
        $unique_id = Token::unique('sessions');
        $request = request();
        $location = Location::get($request->ip());
        // return $location;
        $session = Sessions::create([
            'page_id' => $page->unique_id,
            'unique_id' => $unique_id,
            'ip_address' => $request->ip(),
            'region' => $location->regionName ?? '',
            'country' => $location->countryName ?? '',
            'country_code' =>  $location->countryCode ?? '',
            'city' =>  $location->regionName ?? '',
            'device' => $request->device ?? '',
            'referrer' => $request->referrer ?? ''
        ]);

        $this->createView($session);
        return $session->unique_id;
    }

    function fetchOrCreate($page, $session_id = null){
        if(!$session_id) return $this->create($page);

        $session = Sessions::where([
            'unique_id' => $session_id,
            'page_id' => $page->unique_id
        ])->first();

        if(!$session) return $this->create($page);

        $this->createView($session);
        return $session->unique_id;
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