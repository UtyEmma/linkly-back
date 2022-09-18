<?php

namespace App\Http\Controllers\Links;

use App\Http\Controllers\Controller;
use App\Http\Requests\Links\CreateClickRequest;
use App\Library\Response;
use App\Library\Token;
use App\Models\Clicks;
use App\Models\Page;
use Illuminate\Http\Request;

class ClickController extends Controller{
    
    function create(CreateClickRequest $request, $page_id){
        if(!$page = Page::find($page_id)) return Response::error(404)->json();

        $unique_id = Token::unique('clicks');
        
        Clicks::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'page_id' => $page_id
        ])->toArray());

        return Response::success()->json();
    }

}
