<?php

namespace App\Http\Controllers;

use App\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function createUserActivitySub($iduser, $startdate, $idadvertise, $ids){
        $activity = new UserActivity();
        $activity->iduser           = $iduser;
        $activity->date             = $startdate;
        $activity->idads            = $idadvertise;
        $activity->idsubscription   = $ids;
        $activity->activity         = 'subscribe';

        $activity->save();
    }

    public function createUserActivityUnsub($iduser, $startdate, $idadvertise, $ids){
        $activity = new UserActivity();
        $activity->iduser           = $iduser;
        $activity->date             = $startdate;
        $activity->idads            = $idadvertise;
        $activity->idsubscription   = $ids;
        $activity->activity         = 'unsubscribe';

        $activity->save();
    }

    /**
     * method to load User Subscription lihistory
     * 
     * @param $iduser
     * @return response
     */
    public function subscriptionHistory($iduser){
        
        $info = db::table('ads_subscriptions')
                        ->join('advertises', 'ads_subscriptions.idadvertise', '=', 'advertises.idadvertise')
                        ->select('ads_subscriptions.idsubscription',
                                'ads_subscriptions.idadvertise',
                                'advertises.idadvertisers',
                                'advertises.title',
                                'advertises.content',
                                'advertises.url',
                                'advertises.img')
                        ->where('ads_subscriptions.iduser', $iduser)
                        ->orderby('ads_subscriptions.startdate', 'DESC')
                        ->get();
        
        if($info->count() > 0 ) {   
            
            foreach($info as $new){
                
                $activity = DB::connection('mongodb')->collection('useractivities')
                                ->project(['_id' => 0])
                                ->select('date', 'activity')
                                ->where('idsubscription', '=', $new->idsubscription)
                                ->get();
                $activityArray[] = $activity;
                $dataArray[] = [
                    'idsubscription'  =>   $new->idsubscription,  
                    'idadvertise'  =>   $new->idadvertise,  
                    'idadvertisers'  =>   $new->idadvertisers,  
                    'title'  =>   $new->title,  
                    'content'  =>   $new->content,  
                    'url'  =>   $new->url,  
                    'img'  =>   $new->img,  
                    'activity'  =>   $activity,  
                ];
            }
            
            return response()->json($dataArray);
            
        } else {
            return response()->json([
                'message'  =>  'you dont have subscription yet!'
            ]);
        }                     
    }

}
