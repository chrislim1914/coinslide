<?php

namespace App\Http\Controllers;

use App\Advertise;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AdvertiseController extends Controller {

    /**
     * method to display list of ads
     * 
     * @return response
     */
    public function adsList(){

        /**
         * create union create to merge query without subscriber by using NOT IN condition and insert value of 0
         * 
         * the second query is advertiese with link on subscribe table and count the subscription
         * 
         * then we will create a custom pagination for union query
         */
        $adsnosubs = DB::table('advertises')
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertises.adcategory',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate', 
                                'advertises.startdate',
                                'advertises.enddate',
                                DB::raw("(0) as subscriber"))
                        ->whereNotIn('advertises.idadvertise', DB::table('subscriptions')
                                                            ->select('subscriptions.idadvertise')
                        );

        $adswithsubs = DB::table('advertises')
                        ->join('subscriptions','advertises.idadvertise', '=', 'subscriptions.idadvertise' )
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertises.adcategory',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate', 
                                'advertises.startdate',
                                'advertises.enddate',
                                DB::raw('count(subscriptions.idsubscription) as subscriber'))
                        ->whereNull('subscriptions.enddate')
                        ->union($adsnosubs)
                        ->groupBy('advertises.idadvertise')
                        ->orderBy('subscriber', 'DESC')
                        ->get();

        if($adswithsubs->count() > 0 ) {                
                return response()->json($adswithsubs);
        } else {
            return response()->json([
                "message" => "No advertise are found."
            ]);
        }
    }

    /**
     * method to display list of ads
     * 
     * @return response
     */
    public function bestAds(){

        /**
         * create union create to merge query without subscriber by using NOT IN condition and insert value of 0
         * 
         * the second query is advertiese with link on subscribe table and count the subscription
         * 
         * then we will create a custom pagination for union query
         */
        $adsnosubs = DB::table('advertises')
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertises.adcategory',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate', 
                                'advertises.startdate',
                                'advertises.enddate',
                                DB::raw("(0) as subscriber"))
                        ->whereNotIn('advertises.idadvertise', DB::table('subscriptions')
                                                            ->select('subscriptions.idadvertise')
                        );

        $adswithsubs = DB::table('advertises')
                        ->join('subscriptions','advertises.idadvertise', '=', 'subscriptions.idadvertise' )
                        ->select('advertises.idadvertise',
                                'advertises.idadvertisers',
                                'advertises.adcategory',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img',
                                'advertises.createdate', 
                                'advertises.startdate',
                                'advertises.enddate',
                                DB::raw('count(subscriptions.idsubscription) as subscriber'))
                        ->whereNull('subscriptions.enddate')                        
                        ->union($adsnosubs)
                        ->groupBy('advertises.idadvertise')
                        ->orderBy('subscriber', 'DESC')
                        ->limit(8)
                        ->get();

        if($adswithsubs->count() > 0 ) {                
                return response()->json($adswithsubs);
        } else {
            echo json_encode(
                array("message" => "No advertise are found.")
            );
        }
    }

    /**
     * method to display list of ads by advertiser
     * 
     * @return response
     */
    public function adsListbyAdvertiser($id){

        //create query advertise
        $advertises = DB::table('advertises')
                        ->select('advertises.idadvertise', 'advertises.idadvertisers', 'advertises.adcategory',
                        'advertises.title', 'advertises.content', 'advertises.url',
                        'advertises.img', 'advertises.createdate', 'advertises.startdate',
                        'advertises.enddate' )
                        ->where('advertises.idadvertisers', $id)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertises;

        if($cursor->count() > 0 ) {                
                return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "No advertise are found.")
            );
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
        $advertise->adcategory      = $request->adcategory;
        $advertise->title           = $request->title;
        $advertise->content         = $request->content;
        $advertise->url             = $request->url;
        $advertise->img             = $request->img;
        $advertise->startdate       = $request->startdate;
        $advertise->enddate         = $request->enddate;

        if($advertise->save()) {
            echo json_encode(
                array("message" => "New Ads Created.")
            );
        } else {
            echo json_encode(
                array("message" => "Ads not created.")
            );
        }
    }

    /**
     * method to create Ads
     * 
     * @param Request $request $id
     */
    public function updateAds(Request $request, $id){

        //find Ads info
        $advertise = Advertise::where('idadvertise', $id)->get();

        if($advertise->count() > 0){
            //update ads
            $updateAdvertise = Advertise::where('idadvertise', $id);
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
                    echo json_encode(
                        array("message" => "Ads Info Updated.")
                    );
                } else {
                    echo json_encode(
                        array("message" => "there is nothing to update.")
                    );
                }
        } else {
            echo json_encode(
                array("message" => "Ads not found.")
            );
        }
    }

    /**
     * method to read single ads
     * 
     * @param $id
     */
    public function readAds($id){

        //find Ads info
        $advertise = Advertise::where('idadvertise', $id)->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertise;

        if($cursor->count() > 0 ) {                   
            foreach($cursor as $new) {
                return response()->json([
                    'idadvertise' => $new->idadvertise,
                    'idadvertisers' => $new->idadvertisers,
                    'adcategory' => $new->adcategory,
                    'title' => $new->title,
                    'content' => $new->content,
                    'url' => $new->url,
                    'img' => $new->img,
                    'startdate' => $new->startdate,
                    'enddate' => $new->enddate,
                ]);                
            }
        } else {
            echo json_encode(
                array("message" => "Ads not found.")
            );
        }
    }
}
