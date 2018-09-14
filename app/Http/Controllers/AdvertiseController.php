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

    private $activity;
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
        
        $iduser = $request->iduser;

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
                
                $iduser <= null ? $isSubscribe = false : $isSubscribe = $adsSub->isSubscribe($iduser, $idadvertise);          
                
                $redistag = new RedisController();
                $tag = $redistag->loadAdsTag($idadvertise);

                $array[] = [
                    'iduser'        => $iduser,
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
                    'isSubscribe'   => $isSubscribe,
                    'tag'           => $tag
                ];
            }

            return response()->json([
                'data'      =>  $array,
                'result'    =>  true 
                ]);
        } else {
            return response()-json([
                'message'   => 'no Ads found.',
                'result'    =>  false
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

        $iduser = $request->iduser;

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

                $iduser <= null ? $isSubscribe = false : $isSubscribe = $adsSub->isSubscribe($iduser, $idadvertise);     
                
                $redistag = new RedisController();
                $tag = $redistag->loadAdsTag($idadvertise);

                $array[] = [
                    'iduser'        => $iduser,
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
                    'isSubscribe'   => $isSubscribe,
                    'tag'           => $tag
                ];
            }
            return response()->json([
                'data'      =>  $array,
                'result'    =>  true 
            ]);
        } else {
            return response()->json([
                'message' => 'no Ads found.',
                'result'    =>  false
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
            return response()->json([
                'data'      =>  $ads,
                'result'    =>  true 
            ]);
        } else {
            return response()->json([
                'mesaage' => 'You dont have subscription yet',
                'result'    =>  false
            ]);
        }
    }    

    /**
     * method to create Ads
     * 
     * @param Request $request
     */
    public function createAds(Request $request){

        $advertise = new Advertise();
        $advertise->idadvertisers   = $request->idadvertisers;
        $advertise->title           = $request->title;
        $advertise->content         = $request->content;
        $advertise->url             = $request->url;
        $advertise->img             = $request->img;
        $advertise->startdate       = $request->startdate;
        $advertise->enddate         = $request->enddate;

        if($advertise->save()) {

            /**
             * instantiate TagController and save on tag table
             * then get id everytime its save on $taglist
             */
            $tagCont = new TagController();
            $taglist = $tagCont->createAdsTag($request->tag);
            $idads = $advertise->id;

            /**
             * loop thru $taglist
             * then instantiate RedisController
             * hset everything on $taglist
             */
            for ($i = 0; $i < count($taglist); $i++) {
                
                $redis = new RedisController();
                $redis->adsTag($taglist[$i][0], $idads);
            }

            return response()->json([
                'mesaage'   => '',
                'result'    =>  true
            ]);
        } else {            
            return response()->json([
                'mesaage'   => 'Ads not created.',
                'result'    =>  false
            ]);
        }
    }

    /**
     * method to create Ads
     * 
     * @param Request $request $id
     */
    public function updateAds(Request $request, $idads){

        //find Ads info
        $advertise = Advertise::where('idadvertise', $idads)->get();

        if($advertise->count() > 0){
            //update ads
            $updateAdvertise = Advertise::where('idadvertise', $idads);
                if($updateAdvertise->update([
                                    'idadvertisers' => $request->idadvertisers,
                                    'adcategory'    => $request->adcategory,
                                    'title'         => $request->title,
                                    'content'       => $request->content,
                                    'url'           => $request->url,
                                    'img'           => $request->img,
                                    'startdate'     => $request->startdate,
                                    'enddate'       => $request->enddate
                                    ])) {

                    /**
                     * instantiate TagController and save on tag table
                     * then get id everytime its save on $taglist
                     */
                    $tagCont = new TagController();
                    $taglist = $tagCont->createAdsTag($request->tag);
                    $idads = $advertise->id;

                    $redis = new RedisController();
                    $delTag = $redis->deleteAdsTag($idads);

                    /**
                     * loop thru $taglist
                     * then instantiate RedisController
                     * hset everything on $taglist
                     */
                    for ($i = 0; $i < count($taglist); $i++) {   
                        $redis->adsTag($taglist[$i][0], $idads);
                    }
                    return response()->json([
                        'mesaage'   => '',
                        'result'    =>  true
                    ]);
                } else {
                    return response()->json([
                        'mesaage'   => 'there is nothing to update.',
                        'result'    =>  false
                    ]);
                }
        } else {
            return response()->json([
                'mesaage'   => 'Ads not found.',
                'result'    =>  false
            ]);
        }
    }

    /**
     * method to read single ads
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function readAds(Request $request){

        //find Ads info
        $advertise = DB::table('advertises')
                        ->join('advertisers', 'advertises.idadvertisers', '=', 'advertisers.idadvertiser')
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertisers.company_name',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate',
                                'advertises.startdate',
                                'advertises.enddate')
                        ->where('idadvertisers', $request->idadvertiser)
                        ->where('idadvertise', $request->idads)
                        ->get();
        // $advertise = Advertise::where('idadvertisers', $request->idadvertiser)->where('idadvertise', $request->idads)->get();

        $redistag = new RedisController();
        $tag = $redistag->loadAdsTag($request->idads);

        //check if the user is subscribed to the ads he trying to view
        $adsSub = new AdsSubscriptionController();
        $request->iduser == null ? $isSubscribe = false : $isSubscribe = $adsSub->isSubscribe($request->iduser, $request->idads); 

        if($advertise->count() > 0 ) {                   
            foreach($advertise as $new) {
                $viewadvertise =  [
                    'idadvertise'   => $new->idadvertise,
                    'idadvertisers' => $new->idadvertisers,
                    'advertiser'    => $new->company_name,
                    'title'         => $new->title,
                    'content'       => $new->content,
                    'url'           => $new->url,
                    'img'           => $new->img,
                    'createdate'    => $new->createdate,
                    'startdate'     => $new->startdate,
                    'enddate'       => $new->enddate,
                    'isSubscribe'   => $isSubscribe,
                    'tag'           => $tag
                ];                
            }
        } else {
            $viewadvertise = ["message" => "Ads not found."];
        }

        $otherAdsByAdvertiser = Advertise::where('idadvertise', '<>', $request->idads)
                                            ->where('idadvertisers', $request->idadvertiser)
                                            ->orderBy('idadvertise', 'desc')->get();

        if($otherAdsByAdvertiser->count() <= 0 ) { 
            $otherAdsByAdvertiser = ["message" => "no other ads found."];
        }

        $arrayData = [
            'toviewAds' => $viewadvertise,
            'otherAds'  => $otherAdsByAdvertiser,
        ];
        return response()->json($arrayData);
    }
}
