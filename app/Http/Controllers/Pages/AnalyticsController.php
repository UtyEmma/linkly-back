<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Library\Response;
use App\Models\Clicks;
use App\Models\Links;
use App\Models\Page;
use App\Models\Sessions;
use App\Models\Views;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller{

    private $months = ["Jan", "Feb",  "Mar",  "Apr",  "May",  "Jun",  "Jul",  "Aug", "Sep", 'Oct', 'Nov', 'Dec'];

    /**
     * Request expects the following params
     * - range of days
    */
    function activity(Request $request, $page_id){
        if(!$page = Page::find($page_id)) return Response::error(404)->json("Page not found");
        return Response::success()->json([
            'activity' => $this->traffic($page),
            'devices' => $this->devices($page),
            'referrers' => $this->referrer($page),
            'countries' => $this->country($page),
            'city' => $this->city($page),
            'visits' => $page->visits()->count()
        ]);
    }

    function traffic(Page $page){
        $visits = $page->visits()
                        ->select(DB::raw("count(*) as visits, date_format(created_at, '%b') as month"))
                        ->groupBy('month')->get();

        $clicks = $page->clicks()
                ->select(DB::raw("count(*) as clicks, date_format(created_at, '%b') as month"))
                ->groupBy('month')->get();

        $views = $page->views()
                ->select(DB::raw("count(*) as views, date_format(created_at, '%b') as month"))
                ->groupBy('month')->get();

        return array_map(function($value) use($visits, $clicks, $views) {
                        $stat['month'] = $value;
                        $visits_arr = $visits->where('month', $value)->all();
                        $clicks_arr = $clicks->where('month', $value)->all();
                        $views_arr = $views->where('month', $value)->all();
                        $stat['visits'] = count($visits_arr) ? $visits_arr[0]['visits'] : 0;
                        $stat['clicks'] = count($clicks_arr) ? $clicks_arr[0]['clicks'] : 0;
                        $stat['views'] = count($views_arr) ? $views_arr[0]['views'] : 0;
                        return $stat;
                }, $this->months);
    }

    function devices(Page $page){
        return $page->visits()->select("device", DB::raw("count(*) as total"))->groupBy('device')->get();
    }

    function referrer(Page $page){
        return $page->visits()->select("referrer", DB::raw("count(*) as total"))->groupBy('referrer')->get();
    }
    
    function country(Page $page){
        return $page->visits()->select("country", DB::raw("count(*) as value, country_code"))->groupByRaw('country, country_code')->orderBy('value')->get();
    }

    function city(Page $page){
        return $page->visits()->select("region", DB::raw("count(*) as total"))->groupBy('region')->count();
    }
}
