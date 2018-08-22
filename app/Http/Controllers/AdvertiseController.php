<?php

namespace App\Http\Controllers;

use App\Advertise;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\DateTimeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RedisController;

class AdvertiseController extends Controller {

    /**
     * method to load all new ads
     * order by createddate desc
     * 
     * @return response
     */
    public function newAds(){

        $ads = DB::table('advertises')
                        ->join('advertisers', 'advertises.idadvertisers', '=', 'advertisers.idadvertiser')
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertisers.company_name',
                                'advertisers.logo',
                                'advertises.adcategory',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate', 
                                'advertises.startdate')
                        ->whereNull('advertises.enddate')
                        ->orderBy('advertises.createdate', 'desc')
                        ->get();
        $cursor = $ads;

        if($cursor->count() > 0 ) {
            return response()->json($cursor);
        } else {
            return response()-json([
                'message' => 'no Ads found.'
            ]);
        }
    }

    public function popularAds(){
        $adsSub = DB::table('ads_subscriptions')
                            ->select('ads_subscriptions.idadvertise', 'ads_subscriptions.startdate')
                            ->whereNull('ads_subscriptions.enddate')
                            ->get();
        $count =  $adsSub->count();
                            
        foreach($adsSub as $newSub){
            
            $utility = new UtilityController();
            $popular = $utility->popularAds($newSub->startdate);
            if($popular){
                $ads = DB::table('advertises')
                ->join('ads_subscriptions', 'advertises.idadvertise', '=', 'ads_subscriptions.idadvertise' )
                ->join('advertisers', 'advertises.idadvertisers', '=', 'advertisers.idadvertiser')                        
                ->select('advertises.idadvertise',
                        'advertises.idadvertisers',
                        'advertisers.company_name',
                        'advertisers.logo',
                        'advertises.title',
                        'advertises.content',
                        'advertises.url',
                        'advertises.img',
                        'advertises.createdate', 
                        'advertises.startdate',
                        DB::raw('count(ads_subscriptions.idsubscription) as subscription'))
                ->whereNull('advertises.enddate')
                ->where('advertises.idadvertise', $newSub->idadvertise)
                ->orderBy('subscription', 'desc')
                ->groupBy('advertises.idadvertise')
                ->get();
            $cursor = $ads;
            return response()->json($cursor);
            }
        }   
    }
}
