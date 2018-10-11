<?php

namespace App\Http\Controllers;

use App\Refferal;
use App\User;
use Illuminate\Http\Request;

class RefferalController extends Controller
{
    
    /**
     * method to save new refferal
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function insertRefferal(Request $request){

        //check if the new user is already in the refferal table
        $check = Refferal::where('recommended_to_nickname', $request->recommended_to_nickname)->get();

        if($check->count() > 0){
            return response()->json([
                'message'   =>  'theres something wrong. this name is already been use in refferal bonus.',
                'result'    => false
            ]);
        }

        //check if the recommender nickname is in the user table
        $checkRefferedBy = User::where('nickname', $request->recommended_by_nickname)->get();

        if($checkRefferedBy->count() <= 0){
            return response()->json([
                'message'   =>  'the refferal name dont exist.',
                'result'    => false
            ]);
        }
        
        //ok save the new refferal info
        $refferal = new Refferal();
        $refferal->recommended_by_nickname = $request->recommended_by_nickname;
        $refferal->recommended_to_nickname = $request->recommended_to_nickname;

        if($refferal->save()){
            return response()->json([
                'message'   =>  'New Referral info save.',
                'result'    => true
            ]);
        }else{
            return response()->json([
                'message'   =>  'failed to save refferal info.',
                'result'    => false
            ]);
        }
    }
}
