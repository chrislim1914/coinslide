<?php

namespace App\Http\Controllers;

use App\Advertise;
use App\Advertiser;
use App\AdvertiserBanner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\RedisController;

class AdvertiserController extends Controller
{

    /**
     * method to load advertiser info, advertiser banner, and ads
     * 
     * @param $idadvertiser
     * 
     * @return response
     */
    public function advertiserInfo($idadvertiser){

        //load advertiser
        $advertiser = Advertiser::where('idadvertiser', $idadvertiser)
                                ->where('delete', 0)->get();
        if($advertiser->count() <= 0){
            $advertiser = ['message' => 'advertiser not found or account is deactivated'];
        }

        //load advertiser banner
        $banner = AdvertiserBanner::where('idadvertiser', $idadvertiser)->get();
        if($banner->count() <= 0){
            $banner = ['message' => 'advertiser dont have banner'];
        }

        //load advertiser ads
        $ads = Advertise::where('idadvertisers', $idadvertiser)->orderBy('idadvertise', 'desc')->get();
        if($ads->count() <= 0){
            $ads = ['message' => 'advertiser dont have advertisement yet!'];
        }

        //load advertiser tags
        $tag = new RedisController();
        $advertisertag = $tag->loadAdvertiserTag($idadvertiser);

        //load all in an array
        $arrayData[] = [
            'advertiser' => $advertiser,
            'banner'     => $banner,
            'ads'        => $ads,
            'tag'        => $advertisertag
        ];

        return response()->json([                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
            'data'      => $arrayData,
            'result'    =>  true
        ]);

    }

    /**
     * method to create Advertiser
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function createAdvertiser(Request $request){
        
        $advertiser = new Advertiser();

        $advertiser->idadvertiser               = $request->idadvertiser;
        $advertiser->company_name               = $request->company_name;
        $advertiser->business_registration      = $request->business_registration;
        $advertiser->business_category          = $request->business_category;
        $advertiser->representative_name        = $request->representative_name;
        $advertiser->representative_contactno   = $request->representative_contactno;
        $advertiser->company_website            = $request->company_website;
        $advertiser->email                      = $request->email;

        /**
         * email sender here to verify the registration
         * 
         * also create new table to log all created verification code
         * to match the advertiser code entry
         */

        if($advertiser->save()) {

             /**
             * instantiate TagController and save on tag table
             * then get id everytime its save on $taglist
             */
            $tagCont = new TagController();
            $taglist = $tagCont->createAdvertiserTag($request->tag);
            $idads = $advertiser->id;

            /**
             * loop thru $taglist
             * then instantiate RedisController
             * hset everything on $taglist
             */
            for ($i = 0; $i < count($taglist); $i++) {
                
                $redis = new RedisController();
                $redis->advertiserTag($taglist[$i][0], $idads);
            }
             //send mail here
                // $verificationCode = base64_encode(str_random(12));
                // $send = new SendMail();
                // $sendit = $send->sendMail($request->email,$verificationCode);
            return response()->json([
                'message'   => 'Email has been sent.',
                'result'    =>  true
            ]);
        } else {
            return response()->json([
                'message'   => 'failed to create user.',
                'result'    => false
            ]);
        }
    }

    /**
     * method to save the newly registered advertiser password
     * 
     * @param Request $request, $id
     * 
     * @return response
     */
    public function insertPassword(Request $request, $id){

        //first retrieved the advertiser
        $advertiser = Advertiser::where('idadvertiser', $id)->get();

        if($advertiser->count() > 0){
            $hash = new UtilityController();
            $insertPass = Advertiser::where('idadvertiser', $id);
            if($insertPass->update([
                'password' => $hash->hash($request->password)//password hash
            ])){
                return response()->json([
                    'message'   => 'Password is set.',
                    'result'    =>  true
                ]);
            } else {
                return response()->json([
                    'message'   => 'Failed to set password.',
                    'result'    =>  false
                ]);
            }
        } else {
            return response()->json([
                'message'   => 'advertiser not found.',
                'result'    =>  false
            ]);
        }
    }
}
