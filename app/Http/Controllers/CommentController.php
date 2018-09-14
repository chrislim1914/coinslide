<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;


class CommentController extends Controller {

    /**
     * method to post comment
     * 
     * @param Request $request
     *  
     * @return response
     */
    public function postComment(Request $request){

        $postComment = new Comment();
        $postComment->idcontent = $request->idcontent;
        $postComment->iduser = $request->iduser;
        $postComment->content = $request->content;

        if($postComment->save()){
            return response()->json([
                "message" => "comment posted."
            ]);
        } else {
            return response()->json([
                "message" => "failed to post comment."
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
    public function deleteComment(Request $request){

        //find first the comment info
        $comment = Comment::where('idcomment', $request->idcomment)
                            ->get();

        if($comment->count() > 0 ){   
            //load the needed info for checking 
            foreach($comment as $new){
                    $iduser = $new->iduser;
             }
             // now check if the client is legit to delete
                if($iduser == $request->iduser){
                    $delete = Comment::where('idcomment', $request->idcomment );
                    if($delete->update([
                            'delete' => 1
                        ])){
                        return response()->json([
                            "message" => "comment deleted."
                        ]);
                    } else {
                        return response()->json([
                            "message" => "comment already deleted."
                        ]);
                    }
                } else {
                    return response()->json([
                        "message" => "you are not allowed to delete this post."
                    ]);
                }
        } else {
            return response()->json([
                "message" => "failed in retrieving comment info."
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
    public function countComment($idcontent){
        $count = Comment::where('idcontent', $idcontent)
                        ->where('delete', 0)
                        ->count();

        if($count){
            return response()->json([
                "comment" => "$count"
            ]);
        } else {
            return response()->json([
                "comment" => "0"
            ]);
        }
    }

    /**
     * method to load all comment on single content
     * 
     * @param $idcontent
     * 
     * @return response 
     */
    public function loadComment($idcontent){

        $current = new UtilityController();

        //load comments
        $comment = DB::table('comments')
                        ->select('comments.idcomment',
                                'comments.idcontent',
                                'comments.iduser',
                                'comments.content',
                                'comments.createdate',
                                'comments.modifieddate')
                        ->where('comments.idcontent', $idcontent)
                        ->where('comments.delete', 0)
                        ->orderBy('comments.idcomment', 'DESC')
                        ->get();

        $cursor = $comment;

        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcomment      = $new->idcomment;
                $idcontent      = $new->idcontent;
                $iduser         = $new->iduser;
                $content        = $new->content;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);                

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
                    'idcomment'     => $idcomment,
                    'idcontent'     => $idcontent,
                    'userinfo'      => $iduser,
                    'content'       => $content,
                    'createdate'    => $createdate,
                    'modifieddate'  => $modifieddate,
                    'timelapse'     => $timelapse,
                    'usernickname'  => $user,
                    'profilephoto' => $userinfo
                ];
                
            }
            
            return response()->json($array);
        } else {
            echo json_encode(
                array("message" => "No Comments are found.")
            );
        }
    }

    /**
     * method to get user action on comment table
     * 
     * @param $iduser, $idcontent
     * 
     * @return mix
     */
    public function loadCommentUserInteraction($iduser, $idcontent){
        
        //load comments
        $comment = DB::table('comments')
                        ->select('comments.createdate')
                        ->where('comments.idcontent', $idcontent)
                        ->where('comments.iduser', $iduser)
                        ->where('comments.delete', 0)
                        ->orderBy('comments.idcomment', 'desc')
                        ->limit(1)
                        ->get();

        if($comment->count() > 0){
            foreach($comment as $qwer){
                return $lcon = $qwer->createdate;
            }
        } else {
            return 'no activity';
        }

    }

    /**
     * method to count comment by content
     * 
     * @param $idcontent
     * 
     * @return mix
     */
    public function countCommentforContentList($idcontent){
        $count = Comment::where('idcontent', $idcontent)
                        ->where('delete', 0)
                        ->count();

        if($count){
            return json_encode($count);
        } else {
            return json_encode(0);
        }
    }
}
