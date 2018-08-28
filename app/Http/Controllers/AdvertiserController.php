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

        //load all in an array
        $arrayData[] = [
            'advertiser' => $advertiser,
            'banner'     => $banner,
            'ads'        => $ads,
        ];

        return response()->json($arrayData);
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
             //send mail here
                // $verificationCode = base64_encode(str_random(12));
                // $send = new SendMail();
                // $sendit = $send->sendMail($request->email,$verificationCode);
            return response()->json([
                'message'   => 'Email has been sent.'
            ]);
        } else {
            return response()->json([
                'message'   => 'failed to create user.'
            ]);
        }
    }
}
