<?php

namespace App\Http\Controllers\Links;

use App\Http\Controllers\Controller;
use App\Http\Requests\Link\CreateLinkRequest;
use App\Http\Requests\Links\ReorderLinksRequest;
use App\Http\Requests\Links\UpdateLinkRequest;
use App\Library\Base64;
use App\Library\FileHandler;
use App\Library\Response;
use App\Library\Token;
use App\Models\Links;
use App\Models\Page;
use Illuminate\Http\Request;

class LinksController extends Controller {

    private $base64;

    function __construct(Base64 $base64){
        $this->base64 = $base64;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Page $page) {
        return Response::success()->json([
            'links' => $page->links()->with(['clicks'])->orderBy('position', 'desc')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLinkRequest $request, Page $page) {
        $user = auth()->user();
        $unique_id = Token::unique('links');
        $links = $page->links;

        $link = Links::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'status' => 'draft',
            'user_id' => $user->unique_id,
            'page_id' => $page->unique_id,
            'position' => count($links) < 1 ? 1 : count($links) + 1
        ])->toArray());

        return Response::success()->json("Link Created", [
            'links' => $page->links()->with(['clicks'])->orderBy('position', 'desc')->get()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Links  $links
     * @return \Illuminate\Http\Response
     */
    public function show($page_id, $link_id) {
        if(!$link = Links::where([
            'unique_id' => $link_id,
            'page_id' => $page_id
        ])->with(['clicks'])->first()) return Response::error()->json("Link not Found");

        return Response::success()->json([
            'link' => $link
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Links  $links
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLinkRequest $request, Page $page, $link) { 
        // return response($request->icon);       
        if(!$link = Links::with(['clicks'])->find($link)) return Response::error()->json("Link not found");
        
        $link->update($request->safe()->merge([
            'status' => $this->setStatus($request),
            'icon' => $this->setIcon($request, $this->base64)
        ])->toArray());

        $links = $page->links()->with(['clicks'])->orderBy('position', 'desc')->get();
        
        return Response::success()->json("Link Updated", [
            'link' => $link,
            'links' => $links
        ]);
    }

    private function setIcon(Request $request, Base64 $base64){
        if($request->thumbnail){
            if($request->thumbnail === 'image') return $base64->parse($request->icon)->check()->type(['image/png'])->upload(public_path('/images/storage'));                
            if($request->thumbnail === 'icon') return $request->icon;
        }
        return null;
    }

    public function reorder(ReorderLinksRequest $request, $page_id){
        $page = Page::find($page_id);
        $links = $request->links;
        $count = count($links);
        
        for ($i=0; $i < count($links); $i++) { 
            Links::find($links[$i])->update([
                'position' => $count - $i
            ]);
        }
        
        $links = $page->links()->with(['clicks'])->orderBy('position', 'desc')->get();
        return Response::success()->json([
            'links' => $links
        ]);
    }

    private function setStatus(Request $request){
        return $request->filled('status') 
                            ? $this->status($request->filled(['title', 'url']), true, $request->status) 
                            : $this->status($request->filled(['title', 'url']));
    }

    private function status($status, $truthy = false, $val = false){
        return $status ? ($truthy ? $val : 'published')  : 'draft';
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Links  $links
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page, $link) {
        if(!$link = Links::with(['clicks'])->find($link)) return Response::error()->json("Link not found");
        $link->delete();
        $links = $page->links()->with(['clicks'])->orderBy('position', 'desc')->get();
        return Response::success()->json("Link Deleted", [
            'links' => $links
        ]);
    }

    public function shorten(Page $page){
        $shorturl = Token::text([6, 9], 'links', 'shorturl');
        return Response::success()->json([
            'shorturl' => strtolower($shorturl)
        ]);
    }
}
