<?php

namespace App\Http\Controllers;

use App\AdvertiserBanner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\UtilityController;

class AdvertiserBannerController extends Controller
{
    /**
     * method to read all banner info by advertiser
     * this list list will be filter on the front-end
     * 
     * @param $id
     * @return response
     */
    public function bannerListbyAdvertiser($idadvertiser){

        //create query advertise
        $advertises = DB::table('advertiser_banners')
                        ->select('advertiser_banners.idadvertiser_banner',
                                'advertiser_banners.idadvertiser',
                                'advertiser_banners.img',
                                'advertiser_banners.position',
                                'advertiser_banners.use')
                        ->where('advertiser_banners.idadvertiser', $idadvertiser)
                        ->orderBy('advertiser_banners.position', 'asc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertises;

        if($cursor->count() > 0 ) {  
                return response()->json([
                    'data'      =>  $cursor,
                    'result'    =>  true 
                ]);
        } else {
            return response()->json([
                'mesaage' => 'failed to save banner content',
                'result'    =>  false
            ]);
        }        
    }


    /**
     * method to save banner
     * 
     * @param Request $request, $idadvertiser
     * 
     * @return response
     */
    public function createAdvertiserBanner(Request $request, $idadvertiser){

        //get the image file
        $photo = $request->file('image');

        $utility = new UtilityController();
        $newImage = $utility->advertiserBannerResize($photo);

        //set new name for image to save on database
        $newName = 'assets/ads_banner/'.time().'.'.$photo->getClientOriginalExtension(); 

        //set directory to save the file
        $destinationPath = $utility->public_path('/');

        //save to image to public/assets/banner folder
        $newImage->save($destinationPath.'/'.$newName,80);

        $AdsBanner = new AdvertiserBanner();
        $AdsBanner->idadvertiser = $idadvertiser;
        $AdsBanner->img = $newName;
        $AdsBanner->position = $request->position;
        $AdsBanner->use = $request->use;

        if($AdsBanner->save()){
            return response()->json([
                'mesaage'   => 'New banner save!',
                'result'    =>  true
            ]);
        }else{
            return response()->json([
                'mesaage' => 'failed to save banner content',
                'result'    =>  false
            ]);
        }

    }

    /**
     * method to update banner
     * 
     * @param Request $request, $idadvertiser, $idbanner
     * 
     * @return response
     */
    public function updateAdvertiserBanner(Request $request, $idadvertiser, $idbanner){

        $banner = AdvertiserBanner::where('advertiser_banners.idadvertiser', $idadvertiser)
                                    ->where('advertiser_banners.idadvertiser_banner', $idbanner)->get();
        
        if($banner->count() > 0){
            //get the image file
            $photo = $request->file('image');

            $utility = new UtilityController();
            $newImage = $utility->advertiserBannerResize($photo);

            //set new name for image to save on database
            $newName = 'assets/ads_banner/'.time().'.'.$photo->getClientOriginalExtension(); 

            //set directory to save the file
            $destinationPath = $utility->public_path('/');

            //save to image to public/assets/banner folder
            $newImage->save($destinationPath.'/'.$newName,80);

            $AdsBanner = AdvertiserBanner::where('advertiser_banners.idadvertiser_banner', $idbanner);
            $AdsBanner->idadvertiser = $idadvertiser;
            $AdsBanner->img = $newName;
            $AdsBanner->position = $request->position;
            $AdsBanner->use = $request->use;

            if($AdsBanner->update([
                'idadvertiser'  => $idadvertiser,
                'img'           => $newName,
                'position'      => $request->position,
                'use'           => $request->use,
                ])){
                return response()->json([
                    'mesaage'   => 'banner updated!',
                    'result'    =>  true
                ]);
            }else{
                return response()->json([
                    'mesaage'   => 'failed to update',
                    'result'    =>  true
                ]);
            }
        }else{
            return response()->json([
                'mesaage'   => 'Error loading Banner info.',
                'result'    =>  true
            ]);
        }
    }

    /**
     * method to load sigle advertiser banner
     * 
     * @param $idadvertiser, $idbanner
     * 
     * @return response
     */
    public function loadSingleBanner($idadvertiser, $idbanner){
        $banner = AdvertiserBanner::where('advertiser_banners.idadvertiser', $idadvertiser)
                                    ->where('advertiser_banners.idadvertiser_banner', $idbanner)->get();
        
        if($banner->count() > 0){
            return response()->json([                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                'data'      => $banner,
                'result'    =>  true
            ]);
        }else{
            return response()->json([
                'mesaage'   => 'Advertiser dont have banner yet.',
                'result'    =>  false
            ]);
        }
    }
}
