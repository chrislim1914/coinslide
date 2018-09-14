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
     * @param Request $request
     * 
     * @return response
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
                            return response()->json([
                                "message" => "like."
                            ]);
                        }
                        break;
                    }
                    case 1: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 2])) {
                            return response()->json([
                                "message" => "value change."
                            ]);
                        }
                        break;
                    }
                    case 2: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 1])) {
                            return response()->json([
                                "message" => "like."
                            ]);
                        }
                        break;
                    }
                }                
           }

        } else {

            //like the freash content
            $like = new Likes();
            $like->idcontent = $request->idcontent;
            $like->iduser    = $request->iduser;
            $like->islike    = 1;

            if($like->save()) {
                return response()->json([
                    "message" => "like."
                ]);
            } else {
                return response()->json([
                    "message" => "Failed so like the content."
                ]);
            }
        }
    }

    /**
     * method to save dislike
     * 
     * @param Request $request
     * 
     * @return response
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
                            return response()->json([
                                "message" => "value change."
                            ]);
                        }
                        break;
                    }
                    case 1: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 0])) {
                            return response()->json([
                                "message" => "disliked."
                            ]);
                        }
                        break;
                    }
                    case 2: {
                        $updatelike = Likes::where('idlike', $lookup->idlike);
                        if($updatelike->update(['islike' => 0])) {
                            return response()->json([
                                "message" => "disliked."
                            ]);
                        }
                        break;
                    }
                }                
           }
        } else {
            $like = new Likes();
            $like->idcontent = $request->idcontent;
            $like->iduser    = $request->iduser;
            $like->islike    = 0;

            if($like->save()) {
                return response()->json([
                    "message" => "disliked."
                ]);
            } else {
                return response()->json([
                    "message" => "failed to disliked."
                ]);
            }
        }
    }

    /**
     * method to count like on a single content
     * 
     * @param $idcontent
     * 
     * @return json_encode
     */
    public function countLike($idcontent){

        //retrieve first if idcontent is already in like table
        $look  = Likes::where('idcontent', $idcontent)
                        ->get();
        
        if($look->count() > 0 ){
            $countlike = Likes::where('idcontent', $idcontent)
                                ->where('islike', 1)
                                ->count();
            return json_encode($countlike);
        } else {
            return json_encode(0);
        }
    }

    /**
     * method to count dislike on a single content
     * 
     * @param $idcontent
     * 
     * @return json_encode
     */
    public function countDislike($idcontent){

        //retrieve first if idcontent is already in like table
        $look  = Likes::where('idcontent', $idcontent)
                        ->where('islike', 0)
                        ->get();
        
        if($look->count() > 0 ){
            $countdislike = Likes::where('idcontent', $idcontent)
                                ->where('islike', 0)
                                ->count();
            
            return json_encode($countdislike);
        } else {
            return json_encode(0);
        }
    }

    /**
     * method to load user activity on likes table
     * 
     * @param $iduser, $idcontent
     * 
     * @return mix
     */
    public function loadUserInteraction($iduser, $idcontent){

        //retrieve first the content
        $look  = DB::table('likes')
                        ->select('likes.islike')
                        ->where('likes.iduser', $iduser)
                        ->where('likes.idcontent', $idcontent)
                        ->get();
        
        if($look->count() > 0){
            foreach($look as $new){
                return $new->islike;
            }
            return $look;
        } else {
            return 'no interaction';
        }
    }

    public function isLike($iduser, $idcontent){
        $look  = DB::table('likes')
                    ->select('likes.islike')
                    ->where('likes.iduser', $iduser)
                    ->where('likes.idcontent', $idcontent)
                    ->where('likes.islike', 1)
                    ->get();

        if($look->count() > 0){            
            return true;
        } else {
            return false;
        }
    }

    public function isDislike($iduser, $idcontent){
        $look  = DB::table('likes')
                    ->select('likes.islike')
                    ->where('likes.iduser', $iduser)
                    ->where('likes.idcontent', $idcontent)
                    ->where('likes.islike', 0)
                    ->get();

        if($look->count() > 0){            
            return true;
        } else {
            return false;
        }
    }
}
