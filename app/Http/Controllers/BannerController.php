<?php

namespace App\Http\Controllers;

use App\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use App\Http\Controllers\UtilityController;


class BannerController extends Controller
{
    /**
     * method to retrieved all banner
     * 
     * @return Response
     */
    public function readAllBanners(){
                
        $Banner  = Banner::orderBy('idbanner', 'desc')
                        ->get();
        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Banner;

        if($cursor->count() > 0 ) {
            return response()->json([
                'data'      => $cursor,
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message'   => 'No Banner are found.',
                'result'    => true
            ]);
        }
    }

    /**
     * method to retrieved single banner
     * 
     * @param $idbanner
     * @return Response
     */
    public function readBanner($idbanner){

        //get Banner info
        $Banner  = Banner::where('idbanner', $idbanner)->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Banner;

        if($cursor->count() > 0 ) {
            return response()->json([
                'data'      => $cursor,
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message'   => 'No Banner are found.',
                'result'    => true
            ]);
        }
    }

    /**
     * method to display active banner base on the enddate column
     * 
     * @return Responce
     */
    public function activeBanner(){

        $now = Carbon::now();
        //get all banner
        $Banner  = Banner::orderBy('position', 'asc')
                        ->get();       

        if($Banner->count() > 0 ) {
            //new instance then filter by greater then equal against carbon new datetime
            $active = Banner::where('enddate', '>=', $now)->get();

            //load the active banner
            return response()->json([
                'data'      => $active,
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message'   => 'No Banner are found.',
                'result'    => true
            ]);
        }
    }

    /**
     * method to search banner table by title description
     * with pagination
     * 
     * @return Request $request responce 
     */
    public function searchBanner(Request $request){

        /**
         * check if search string is null before we query
         */
        if($request->search == null) {
            return response()->json([
                'message'   => 'Search String is empty.',
                'result'    => true
            ]);
        } else {

            $Banner = Banner::where('title', 'LIKE', "%$request->search%")
                          ->orWhere('description', 'LIKE', "%$request->search%")
                          ->get();

            //the cursor method may be used to greatly reduce your memory usage:
            $cursor = $Banner;

            if($cursor->count() > 0 ) {
                return response()->json([
                    'data'      => $cursor,
                    'result'    => false
                ]);
            } else {                
                return response()->json([
                    'message'   => '"No Banner are found.',
                    'result'    => false
                ]);
            }
        }
    }

    /**
     * method to create banner
     * 
     * @return Request $request 
     * @return responce 
     */
    public function createBanner(Request $request){

        $photo = $request->file('img');

        //instanciate ImageController for resize
        $resize = new UtilityController();
        $newImage = $resize->bannerResize($photo);

        //set new name for image to save on database
        $newBannerName = 'assets/banner/'.time().'.'.$photo->getClientOriginalExtension(); 

        //set directory to save the file
        $destinationPath = $resize->public_path('/');        
       
        //save to image to public/assets/banner folder
        $newImage->save($destinationPath.'/'.$newBannerName,80);

        //instanciate Banner model and save the data
        $banner = new Banner();
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->img = $newBannerName;
        $banner->startdate = $request->startdate;
        $banner->enddate = $request->enddate;
        $banner->position = $request->position;

        if($banner->save()) {
            return response()->json([
                'message'   => 'New Banner Created.',
                'result'    =>  true
            ]);
        } else {
            return response()->json([
                'message'   => 'Banner not created.',
                'result'    =>  false
            ]);
        }
    }

    /**
     * methos to update banner
     * 
     * @return Request $request 
     * @return responce 
     */
    public function updateBanner(Request $request, $idbanner){

        //find banner info
        $banner = Banner::where('idbanner', $idbanner)
                        ->get();

        if($banner->count() > 0 ) {
            //udate banner
            $photo = $request->file('img');

            //instanciate ImageController for resize
            $resize = new UtilityController();
            $newImage = $resize->bannerResize($photo);

            //set new name for image to save on database
            $newBannerName = 'assets/banner/'.time().'.'.$photo->getClientOriginalExtension(); 

            //set directory to save the file
            $destinationPath = $resize->public_path('/');        
        
            //save to image to public/assets/banner folder
            $newImage->save($destinationPath.'/'.$newBannerName,80);

            $updateBanner = Banner::where('idbanner', $idbanner);
            if($updateBanner->update([
                                'title'         => $request->title,
                                'description'   => $request->description,
                                'img'           => $newBannerName,
                                'startdate'     => $request->startdate,
                                'enddate'       => $request->enddate,
                                'position'       => $request->position,
            ])) {
                return response()->json([
                    'message'   => 'Banner Updated.',
                    'result'    =>  true
                ]);
            } else {
                return response()->json([
                    'message'   => 'there is nothing to update.',
                    'result'    =>  false
                ]);
            }
        } else {
            return response()->json([
                'message'   => 'Banner not found.',
                'result'    =>  false
            ]);
        }         
        
    }
}
