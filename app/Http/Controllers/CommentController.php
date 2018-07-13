<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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
                            ->where('idcontent', $request->idcontent)
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

        if($comment->count() > 0 ){
            return response()->json($comment);
        } else {
            return response()->json([
                "message" => "no comments yet!"
            ]);
        }
    }
}
