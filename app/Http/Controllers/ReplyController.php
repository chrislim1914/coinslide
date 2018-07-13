<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ReplyController extends Controller {

    /**
     * method to post reply
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function postReply(Request $request){

        $postComment = new Reply();
        $postComment->idcomment = $request->idcomment;
        $postComment->iduser = $request->iduser;
        $postComment->content = $request->content;

        if($postComment->save()){
            return response()->json([
                "message" => "reply posted."
            ]);
        } else {
            return response()->json([
                "message" => "failed to post reply."
            ]);
        }
    }

    /**
     * method to soft delete comment
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function deleteReply(Request $request){

        //find first the comment info
        $comment = Reply::where('idreply', $request->idreply)
                            ->where('idcomment', $request->idcomment)
                            ->get();

        if($comment->count() > 0 ){   
            //load the needed info for checking 
            foreach($comment as $new){
                    $iduser = $new->iduser;
             }
             // now check if the client is legit to delete
                if($iduser == $request->iduser){
                    $delete = Reply::where('idreply', $request->idreply );
                    if($delete->update([
                            'delete' => 1
                        ])){
                        return response()->json([
                            "message" => "reply deleted."
                        ]);
                    } else {
                        return response()->json([
                            "message" => "reply is already deleted."
                        ]);
                    }
                } else {
                    return response()->json([
                        "message" => "you are not allowed to delete this post."
                    ]);
                }
        } else {
            return response()->json([
                "message" => "failed in retrieving reply info."
            ]);
        }
    }

    /**
     * method to count comment by content
     * 
     * @param $idcontent
     * 
     * @return response
     */
    public function countReply($idcomment){
        $count = Reply::where('idcomment', $idcomment)
                        ->where('delete', 0)
                        ->count();

        if($count){
            return response()->json([
                "reply" => "$count"
            ]);
        } else {
            return response()->json([
                "reply" => "0"
            ]);
        }
    }

    /**
     * method to load all reply by comment
     * 
     * @param $idcomment
     * 
     * @return response
     */
    public function loadReply($idcomment){

        //load replies
        $reply = Reply::where('replies.idcomment', $idcomment)
                        ->where('replies.delete', 0)
                        ->orderBy('replies.idreply', 'DESC')
                        ->get();

        if($reply->count() > 0 ){
            return response()->json($reply);
        } else {
            return response()->json([
                "message" => "no reply yet!"
            ]);
        }
    }    
}
