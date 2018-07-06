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
     */
    public function adsList(){

        //create query advertise
        $advertises = DB::table('advertises')
                        ->select('advertises.idadvertise', 'advertises.idadvertisers', 'advertises.adcategory',
                        'advertises.title', 'advertises.content', 'advertises.url',
                        'advertises.img', 'advertises.createdate', 'advertises.startdate',
                        'advertises.enddate' )
                        ->paginate(5);

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

        if($advertise->count()){
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
