<?php

namespace App\Http\Controllers;

use App\AdvertiserBanner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PasswordEncrypt;

class AdvertiserBannerController extends Controller {

    /**
     * method to read all banner info by advertiser
     * this list list will be filter on the front-end
     * 
     * @param $id
     * @return response
     */
    public function bannerListbyAdvertiser($id){

        //create query advertise
        $advertises = DB::table('advertiser_banners')
                        ->select('advertiser_banners.idadvertiser_banner',
                                'advertiser_banners.idadvertiser',
                                'advertiser_banners.img',
                                'advertiser_banners.position',
                                'advertiser_banners.use')
                        ->where('advertiser_banners.idadvertiser', $id)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertises;

        if($cursor->count() > 0 ) {                
                return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "Advertiser dont have banner yet.")
            );
        }        
    }
}
