<?php

namespace App\Http\Controllers;

use App\Subscriptions;
use App\User;
use App\Advertise;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class SubscriptionController extends Controller
{
    /**
     * method to subscribe to an Ads
     * 
     * first we check if the user, and ads exist on their respective tables
     * 
     * @param Request $request
     * @return $response
     */
    public function subscribe(Request $request){

        if($this->findUser($request->iduser)){
            if($this->findAds($request->idadvertise)){

                $subscribe = new Subscriptions();
                $subscribe->iduser = $request->iduser;
                $subscribe->idadvertise = $request->idadvertise;
                $subscribe->startdate = $this->currentDatetime();

                if($subscribe->save()) {
                    return response()->json([
                        "message" => "Subscribed!"
                    ]);
                } else {
                    return response()->json([
                        "message" => "Failed to subscribe!"
                    ]);
                }

            } else {
                return response()->json([
                    "message" => "cannot find Ads!"
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
    public function unSubscribe(Request $request){

        $check = Subscriptions::where('iduser', $request->iduser)
                                ->where('idadvertise', $request->idadvertise)
                                ->where('enddate', null)
                                ->get();
        
        if($check->count() > 0 ){
            foreach($check as $new){
                $unsubscribe = Subscriptions::where('idsubscription', $new->idsubscription);
                if($unsubscribe->update(["enddate" => $this->currentDatetime()])) {
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
        $advertise = Advertise::where('idadvertise', $idadvertise)
                            ->get();

        if($advertise->count() > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * method to read all subscription filter by user
     * 
     * @param $iduser
     * 
     * @return response
     */
    private function subscriptionList($iduser){

        $sublist = DB::table('subscriptions')
                        ->join()
                        ->join()
                        ->select()
                        ->whereNull('subscriptions.enddate')
                        ->get();
        
        if($sublist->count() > 0 ){
            return response()->json($sublist);
        } else {
            return response()->json([]);
        }
    }

    public function currentDatetime(){
        //create current time using Carbon
        $current = Carbon::now();

        // Set the timezone via DateTimeZone instance or string
        $current->timezone = new \DateTimeZone(getenv('APP_TIMEZONE'));

        return $current;
    }
}
