<?php

namespace App\Http\Controllers;

use App\Advertise;
use App\Http\Controllers\AdsSubscriptionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Carbon;

class AdvertiseController extends Controller {

    /**
     * method to load all new ads
     * order by createddate desc
     * 
     * @param Request $request
     * @return response
     */
    public function newAds(Request $request){

        $current = new UtilityController();       

        $ads = DB::table('advertises')
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
                                'advertises.startdate')
                        ->whereNull('advertises.enddate')
                        ->orderBy('advertises.createdate', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $ads;

        if($cursor->count() > 0 ) {

            /**
             * apparently this will output only one set of data instead of its normal procedure
             * so we use var array[] to fill everytime foreach condition passed
             */
            foreach($cursor as $newAds){
                $idadvertise   = $newAds->idadvertise;
                $idadvertisers = $newAds->idadvertisers;
                $company_name  = $newAds->company_name;
                $logo          = $newAds->logo;
                $title         = $newAds->title;
                $content       = $newAds->content;
                $url           = $newAds->url;
                $img           = $newAds->img;
                $timelapse      = $current->timeLapse($newAds->createdate);
                /**
                 * now we retrieve some other info
                 * 
                 * how many subscription
                 */

                $adsSub = new AdsSubscriptionController();
                $subscribe = $adsSub->countAdsSubscriptionById($idadvertise);

                $request->iduser = null ? $isSubscribe = false : $isSubscribe = $adsSub->isSubscribe($request->iduser, $idadvertise);                

                $array[] = [
                    'idadvertise'   => $idadvertise,
                    'idadvertisers' => $idadvertisers,
                    'company_name'  => $company_name,
                    'logo'          => $logo,
                    'title'         => $title,
                    'content'       => $content,
                    'url'           => $url,
                    'img'           => $img,
                    'timelapse'     => $timelapse,
                    'subscribe'     => $subscribe,
                    'isSubscribe'   => $isSubscribe
                ];
            }

            return response()->json($array);
        } else {
            return response()-json([
                'message' => 'no Ads found.'
            ]);
        }
    }

    /**
     * method to load all popular ads
     * order by no of subscriber on date range (now-72hours) desc
     * 
     * @param Request $request
     * @return response
     */
    public function popularAds(Request $request){

        $current = new UtilityController();     

        //set date now() then deduct 72 hour to get our starting time and date
        $endDate = Carbon::now();
        $startDate = $endDate->subHours(72); 

        $ads = DB::table('advertises')
                        ->join('advertisers', 'advertises.idadvertisers', '=', 'advertisers.idadvertiser')
                        ->join('ads_subscriptions', 'advertises.idadvertise', '=', 'ads_subscriptions.idadvertise')
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
                                DB::raw('count(ads_subscriptions.idadvertise) as subscibe'))
                        ->whereNull('advertises.enddate')
                        ->where('ads_subscriptions.startdate', '>=', $startDate)
                        ->groupBy('advertises.idadvertise')
                        ->orderBy('subscibe', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $ads;

        if($cursor->count() > 0 ) {

            /**
             * apparently this will output only one set of data instead of its normal procedure
             * so we use var array[] to fill everytime foreach condition passed
             */
            foreach($cursor as $newAds){
                $idadvertise   = $newAds->idadvertise;
                $idadvertisers = $newAds->idadvertisers;
                $company_name  = $newAds->company_name;
                $logo          = $newAds->logo;
                $title         = $newAds->title;
                $content       = $newAds->content;
                $url           = $newAds->url;
                $img           = $newAds->img;
                $timelapse      = $current->timeLapse($newAds->createdate);
                /**
                 * now we retrieve some other info
                 * 
                 * how many subscription
                 */

                $adsSub = new AdsSubscriptionController();
                $subscribe = $adsSub->countAdsSubscriptionById($idadvertise);

                $request->iduser = null ? $isSubscribe = false : $isSubscribe = $adsSub->isSubscribe($request->iduser, $idadvertise);                

                $array[] = [
                    'idadvertise'   => $idadvertise,
                    'idadvertisers' => $idadvertisers,
                    'company_name'  => $company_name,
                    'logo'          => $logo,
                    'title'         => $title,
                    'content'       => $content,
                    'url'           => $url,
                    'img'           => $img,
                    'timelapse'     => $timelapse,
                    'subscribe'     => $subscribe,
                    'isSubscribe'   => $isSubscribe
                ];
            }
            return response()->json($array);
        } else {
            return response()-json([
                'message' => 'no Ads found.'
            ]);
        }

    }

    /**
     * method to load User Subscription list
     * 
     * @param $iduser
     * @return response
     */
    public function subscriptionList($iduser){

        $ads = DB::table('advertises')
                    ->join('ads_subscriptions', 'advertises.idadvertise', '=', 'ads_subscriptions.idadvertise')
                    ->select('advertises.idadvertise',
                            'advertises.idadvertisers',
                            'advertises.title',
                            'advertises.content',
                            'advertises.url',
                            'advertises.img')
                    ->where('ads_subscriptions.iduser', $iduser)
                    ->whereNull('ads_subscriptions.enddate')
                    ->orderBy('ads_subscriptions.startdate', 'desc')
                    ->get();
        
        if($ads->count() > 0){
            return response()->json($ads);
        } else {
            return response()->json([
                'mesaage' => 'You dont have subscription yet'
            ]);
        }
    }

    /**
     * method to load User Subscription lihistory
     * 
     * @param $iduser
     * @return response
     */
    public function subscriptionHistory($iduser){

        $ads = DB::table('advertises')
                    ->join('ads_subscriptions', 'advertises.idadvertise', '=', 'ads_subscriptions.idadvertise')
                    ->select('advertises.idadvertise',
                            'advertises.idadvertisers',
                            'advertises.title',
                            'advertises.content',
                            'advertises.url',
                            'advertises.img',
                            'ads_subscriptions.startdate')
                    ->where('ads_subscriptions.iduser', $iduser)
                    ->orderBy('ads_subscriptions.startdate', 'desc')
                    ->get();
        
        if($ads->count() > 0){
            return response()->json($ads);
        } else {
            return response()->json([
                'mesaage' => 'You dont have subscription yet'
            ]);
        }                  
    }
}
