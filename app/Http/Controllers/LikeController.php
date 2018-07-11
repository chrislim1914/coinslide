<?php

namespace App\Http\Controllers;

use App\Likes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LikeController extends Controller
{
    /**
     * method to save like
     * 
     * @return Request $request
     */
    public function like(Request $request){

        //retrieve first if the content is already liked
        $look  = Likes::where('idcontent', $request->idcontent)
                        ->where('iduser', $request->iduser) 
                        ->get();

        if($look->count() > 0 ){
            /**
             * get idlike islike value for referrence 
             * then change the value into 2 to eliminate any referrences on count method
             * 
             * we choose update insted of delete and insert method to avoid index rebuilding
             */
            foreach($look as $lookup){
                switch($lookup->islike){
                    case 0: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 1])) {
                            echo json_encode(
                                array("message" => "liked")
                            );
                        }
                        break;
                    }
                    case 1: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 2])) {
                            echo json_encode(
                                array("message" => "change value.")
                            );
                        }
                        break;
                    }
                    case 2: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 1])) {
                            echo json_encode(
                                array("message" => "liked")
                            );
                        }
                        break;
                    }
                }                
           }

        } else {
            echo json_encode(
                array("message" => "referrence not found.")
            );
        }
    }

    /**
     * method to save dislike
     * 
     * @return Request $request
     */
    public function dislike(Request $request){

        //retrieve first if the content is already disliked
        $look  = Likes::where('idcontent', $request->idcontent)
                        ->where('iduser', $request->iduser) 
                        ->get();

        if($look->count() > 0 ){
            /**
             * get idlike islike value for referrence 
             * then change the value into 2 to eliminate any referrences on count method
             * 
             * we choose update insted of delete and insert method to avoid index rebuilding
             */
            foreach($look as $lookup){
                switch($lookup->islike){
                    case 0: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 2])) {
                            echo json_encode(
                                array("message" => "change value.")
                            );
                        }
                        break;
                    }
                    case 1: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 0])) {
                            echo json_encode(
                                array("message" => "disliked.")
                            );
                        }
                        break;
                    }
                    case 2: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 0])) {
                            echo json_encode(
                                array("message" => "disliked.")
                            );
                        }
                        break;
                    }
                }                
           }
        } else {
            echo json_encode(
                array("message" => "referrence not found.")
            );
        }
    }

    /**
     * method to count like on a single content
     * 
     * @return reponse
     */
    public function contentLike($id){

        //retrieve first if idcontent is already in like table
        $look  = Likes::where('idcontent', $id)
                        ->get();
        
        if($look->count() > 0 ){
            $countlike = DB::table('likes')
                            ->select('likes.idcontent', DB::raw('count(likes.islike) as `like`'))
                            ->where('idcontent', $id)
                            ->where('islike', 1)
                            ->groupBy('likes.idcontent')
                            ->get();
            return response()->json($countlike);
        } else {
            return response()->json([
                    "idlike" => "0",
                    "like" => "0"
                    ]);
        }
    }

    /**
     * method to count dislike on a single content
     * 
     * @return reponse
     */
    public function contentDislike($id){

        //retrieve first if idcontent is already in like table
        $look  = Likes::where('idcontent', $id)
                        ->get();
        
        if($look->count() > 0 ){
            $countlike = DB::table('likes')
                            ->select('likes.idlike', DB::raw('count(likes.islike) as `dislike`'))
                            ->where('idcontent', $id)
                            ->where('islike', 0)
                            ->groupBy('likes.idlike')
                            ->get();
            return response()->json($countlike);
        } else {
            return response()->json([
                    "idlike" => "0",
                    "dislike" => "0"
                    ]);
        }
    }
}
