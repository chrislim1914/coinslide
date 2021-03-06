<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\DB;

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
                'message'   => 'reply posted.',
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message' => 'failed to post reply.',
                'result'    => false
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
                            'message' => 'reply deleted.',
                            'result'    => true
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'reply is already deleted.',
                            'result'    => false
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'you are not allowed to delete this post.',
                        'result'    => false
                    ]);
                }
        } else {
            return response()->json([
                'message' => 'failed in retrieving reply info.',
                'result'    => false
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
                'reply'     => '$count',
                'result'    => true
            ]);
        } else {
            return response()->json([
                'reply'     => '0',
                'result'    => true
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

        $current = new UtilityController();

        //load replies
        $reply = Reply::where('replies.idcomment', $idcomment)
                        ->where('replies.delete', 0)
                        ->orderBy('replies.idreply', 'DESC')
                        ->get();

        $cursor = $reply;

        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idreply      = $new->idreply;
                $idcomment    = $new->idcomment;
                $iduser       = $new->iduser;
                $content      = $new->content;
                $createdate   = $new->createdate;
                $modifieddate = $new->modifieddate;
                $timelapse    = $current->timeLapse($new->createdate);

                $userinfo = DB::connection('mongodb')->collection('userinformations')
                        ->project(['_id' => 0])
                        ->select('profilephoto')
                        ->where('iduser', '=', $iduser)
                        ->get();

                $user = DB::table('users')
                        ->select('users.nickname')
                        ->where('users.iduser', $iduser)
                        ->get();

                $array[] = [
                    'idreply'     => $idreply,
                    'idcomment'   => $idcomment,
                    'iduser'      => $iduser,
                    'content'     => $content,
                    'createdate'  => $createdate,
                    'modifieddate'=> $modifieddate,
                    'timelapse'   => $timelapse,
                    'usernickname'  => $user,
                    'profilephoto' => $userinfo
                ];
                
            }
            
            return response()->json([
                'data'      =>  $array,
                'result'    => false
            ]);
        } else {            
            return response()->json([
                'message'   =>  'No Replies are found.',
                'result'    => false
            ]);
        }
    }    
}
