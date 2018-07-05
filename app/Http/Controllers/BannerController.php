<?php

namespace App\Http\Controllers;

use App\Banner;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;


class BannerController extends Controller {

    /**
     * method to retrieved all banner
     * 
     * @return Response
     */
    public function readAllBanners(){
                
        $Banner  = Banner::orderBy('idbanner', 'desc')
                        ->paginate(5);
        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Banner;

        if($cursor->count() > 0 ) {
            return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "No Banner are found.")
            );
        }
    }

    /**
     * method to retrieved single banner
     * 
     * @param $id
     * @return Response
     */
    public function readBanner($id){

        //get Banner info
        $Banner  = Banner::where('idbanner', $id)->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Banner;

        if($cursor->count() > 0 ) {
            return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "Banner not found.")
            );
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
            echo json_encode(
                array("message" => "No Search String.")
            );
        } else {

            $Banner = Banner::where('title', 'LIKE', "%$request->search%")
                          ->orWhere('description', 'LIKE', "%$request->search%")
                          ->paginate(5);

            //the cursor method may be used to greatly reduce your memory usage:
            $cursor = $Banner;

            if($cursor->count() > 0 ) {
                return response()->json($cursor);
            } else {
                echo json_encode(
                    array("message" => "No Banner are found.")
                );
            }
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
            return response()->json($active);
        } else {
            echo json_encode(
                array("message" => "No Banner are found.")
            );
        }

    }

    /**
     * method to create banner
     * 
     * @return Request $request responce 
     */
    public function createBanner(Request $request){

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->img = $request->img;
        $banner->startdate = $request->startdate;
        $banner->enddate = $request->enddate;
        $banner->position = $request->position;

        if($banner->save()) {
            echo json_encode(
                array("message" => "New Banner Created.")
            );
        } else {
            echo json_encode(
                array("message" => "Banner not created.")
            );
        }
    }

    /**
     * methos to update banner
     * 
     * @return Request $request responce 
     */
    public function updateBanner(Request $request, $id){

        //find banner info
        $banner = Banner::where('idbanner', $id)
                        ->get();

        if($banner->count() > 0 ) {
            //udate banner
            $updateBanner = Banner::where('idbanner', $id);
            if($updateBanner->update([
                                'title'         => $request->title,
                                'description'   => $request->description,
                                'img'           => $request->img,
                                'startdate'     => $request->startdate,
                                'enddate'       => $request->enddate,
                                'position'       => $request->position,
            ])) {
                echo json_encode(
                    array("message" => "Banner Updated.")
                );
            } else {
                echo json_encode(
                    array("message" => "there is nothing to update.")
                );
            }
        } else {
            echo json_encode(
                array("message" => "Banner not found.")
            );
        }         
        
    }
}
