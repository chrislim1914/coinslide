<?php

namespace App\Http\Controllers;

use App\User;
use App\Advertise;
use App\Http\Controllers\UserActivityController;
use App\AdsSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use Illuminate\Support\Carbon;

class AdsSubscriptionController extends Controller
{   

    /**
     * method to subscribe to an Ads
     * 
     * first we check if the user, and ads exist on their respective tables
     * 
     * @param Request $request
     * @return $response
     */
    public function adsSubscribe(Request $request){

        $utility = new UtilityController();        

        if($this->findUser($request->iduser)){
            if($this->findAds($request->idadvertise)){

                //check if the user is already subscribe
                $check = $this->checkIfSubscribe($request->iduser, $request->idadvertise);

                if($check){
                    return response()->json([
                        "message" => "you already subscribe on this ads!"
                    ]);
                }else{
                    $subscribe = new AdsSubscription();
                    $subscribe->iduser = $request->iduser;
                    $subscribe->idadvertise = $request->idadvertise;
                    $subscribe->startdate = $utility->setDatetime();                    

                    if($subscribe->save()) {
                        
                        $subdate = $subscribe->startdate->toDateString();
                        $activity = new UserActivityController();
                        $saveActivity = $activity->createUserActivitySub($subscribe->iduser, $subdate, $subscribe->idadvertise, $subscribe->id);

                        return response()->json([
                            "message" => "Subscribed!"
                        ]);
                    } else {
                        return response()->json([
                            "message" => "Failed to subscribe!"
                        ]);
                    }
                }
            } else {
                return response()->json([
                    "message" => "Error retrieving ads info!"
                ]);
            }
        } else {
            return response()->json([
                "message" => "must login to subscribe!"
            ]);
        }
    }

    /**
     * method to unsubscribe to an Ads
     * 
     * first we check if the user, and ads exist on their respective tables
     * 
     * @param $id
     * @return $response
     */
    public function adsUnsubscribe(Request $request){

        $utility = new UtilityController(); 
        $enddate = $utility->setDatetime();
        $check = AdsSubscription::where('iduser', $request->iduser)
                                ->where('idadvertise', $request->idadvertise)
                                ->where('enddate', null)
                                ->get();
        
        if($check->count() > 0 ){
            foreach($check as $new){
                $unsubscribe = AdsSubscription::where('idsubscription', $new->idsubscription);

                if($unsubscribe->update(["enddate" => $enddate])) {

                    $subdate = $enddate->toDateString();

                    $activity = new UserActivityController();
                    $saveActivity = $activity->createUserActivityUnsub($request->iduser, $subdate, $request->idadvertise, $new->idsubscription);

                    return response()->json([
                        "message" => "unsubscribe!"
                    ]);
                } else {
                    return response()->json([
                        "message" => "failed to unsubscribe!"
                    ]);
                }
            }            
        } else {
            return response()->json([
                "message" => "object not found!"
            ]);
        }
    }

    /**
     * method to count the subscription by idadvertise
     * 
     * @param $idadvertise
     * 
     * @return response
     */
    public function countAdsSubscriptionById($idadvertise){


        //retrieve first if idadvertise is already in ads_subscription table
        $ads = AdsSubscription::where('idadvertise', $idadvertise)->get();

        if($ads->count() > 0){
            $count = AdsSubscription::where('idadvertise', $idadvertise)
                                ->whereNull('enddate')
                                ->count();
            return json_encode($count);
        } else {
            return json_encode(0);
        }
    }

    /**
     * method to find if the current user is subscribe to the ads
     * 
     * @param $iduser
     * 
     * @return Bool
     */
    public function isSubscribe($iduser, $idadvertise){

        //retrieve first if iduser is already in ads_subscription table
        $ads = AdsSubscription::where('iduser', $iduser)
                                ->where('idadvertise', $idadvertise)
                                ->whereNull('enddate')
                                ->get();

        if($ads->count() > 0){
            return true;
        } else {
            return false;
        }
    }
    /**
     * method to find user
     * 
     * @param $iduser
     * @return bool
     */
    private function findUser($iduser){
        //find user
        $user = User::where('iduser', $iduser)
                            ->where('delete', 0)
                            ->get();

        if($user->count() > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * method to find user
     * 
     * @param $idadvertise
     * @return bool
     */
    private function findAds($idadvertise){
        //find advertise
        $ads = Advertise::where('idadvertise', $idadvertise)
                            ->where('enddate', null)
                            ->get();

        if($ads->count() > 0){
            return true;
        } else {
            return false;
        }
    }

    public function checkIfSubscribe($iduser, $idadvertise){
        $check = AdsSubscription::where('iduser', $iduser)
                                ->where('idadvertise', $idadvertise)
                                ->where('enddate', null)
                                ->get();
        if($check->count() > 0){
            return true;
        }else{
            return false;
        }
    }
}
