<?php

namespace App\Http\Controllers;

use App\Subscriptions;
use App\User;
use App\Advertise;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DateTimeController;
use Illuminate\Support\Facades\DB;

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

        $current = new DateTimeController();

        if($this->findUser($request->iduser)){
            if($this->findAds($request->idadvertise)){

                $subscribe = new Subscriptions();
                $subscribe->iduser = $request->iduser;
                $subscribe->idadvertise = $request->idadvertise;
                $subscribe->startdate = $current->setDatetime();

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

        $current = new DateTimeController();

        $check = Subscriptions::where('iduser', $request->iduser)
                                ->where('idadvertise', $request->idadvertise)
                                ->where('enddate', null)
                                ->get();
        
        if($check->count() > 0 ){
            foreach($check as $new){
                $unsubscribe = Subscriptions::where('idsubscription', $new->idsubscription);
                if($unsubscribe->update(["enddate" => $current->setDatetime()])) {
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
    public function subscriptionList($iduser){

        $sublist = DB::table('subscriptions')
                        ->join('advertises', 'subscriptions.idadvertise', '=', 'advertises.idadvertise')
                        ->select('subscriptions.idsubscription',
                        'subscriptions.iduser',
                        'subscriptions.idadvertise',
                        'advertises.idadvertisers',
                        'advertises.adcategory',
                        'advertises.title',
                        'advertises.content',
                        'advertises.img')
                        ->where('subscriptions.iduser', $iduser)
                        ->whereNull('advertises.enddate')
                        ->whereNull('subscriptions.enddate')
                        ->orderBy('advertises.title', 'ASC')
                        ->get();
        
        if($sublist->count() > 0 ){
            return response()->json($sublist);
        } else {
            return response()->json([]);
        }
    }

    /**
     * method to read all recent subscription filter by user
     * 
     * @param $iduser
     * 
     * @return response
     */
    public function recentSubscriptionList($iduser){

        $sublist = DB::table('subscriptions')
                        ->join('advertises', 'subscriptions.idadvertise', '=', 'advertises.idadvertise')
                        ->select('subscriptions.idsubscription',
                        'subscriptions.iduser',
                        'subscriptions.idadvertise',
                        'advertises.idadvertisers',
                        'advertises.adcategory',
                        'advertises.title',
                        'advertises.content',
                        'advertises.img')
                        ->where('subscriptions.iduser', $iduser)
                        ->whereNull('advertises.enddate')
                        ->whereNull('subscriptions.enddate')
                        ->orderBy('subscriptions.startdate', 'DESC')
                        ->get();
        
        if($sublist->count() > 0 ){
            return response()->json($sublist);
        } else {
            return response()->json([]);
        }
    }

    public function subscriptionHistory($iduser){
        $sublist = DB::table('subscriptions')
                        ->join('advertises', 'subscriptions.idadvertise', '=', 'advertises.idadvertise')
                        ->select('subscriptions.idsubscription',
                        'subscriptions.iduser',
                        'subscriptions.idadvertise',
                        'advertises.idadvertisers',
                        'advertises.adcategory',
                        'advertises.title',
                        'advertises.content',
                        'advertises.img',
                        'subscriptions.startdate as subscriptionstart',
                        'subscriptions.enddate as subscriptionend',
                        'advertises.startdate as adsstart',
                        'advertises.enddate as adsend')
                        ->where('subscriptions.iduser', $iduser)
                        ->orderBy('subscriptions.startdate', 'DESC')
                        ->get();
        
        if($sublist->count() > 0 ){
            return response()->json($sublist);
        } else {
            return response()->json([]);
        }
    }
}
