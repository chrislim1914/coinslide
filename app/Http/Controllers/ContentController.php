<?php

namespace App\Http\Controllers;

use App\Content;
use App\UserInfo;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RedisController;

class ContentController extends Controller
{
    /**
     * method to create temporary content 
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function createTemporaryContent(Request $request){

        //get the image file
        $photo = $request->file('image');

        $utility = new UtilityController();

        $newImage = $utility->contentResize($photo);

        //set new name for image to save on database
        $newName = 'assets/content/'.time().'.'.$photo->getClientOriginalExtension(); 

        //set directory to save the file
        $destinationPath = $utility->public_path('/');

        //save to image to public/assets/banner folder
        $newImage->save($destinationPath.'/'.$newName,80);
        
        $Contents = new Content();
        $Contents->iduser = $request->iduser;
        $Contents->title = $request->title;
        $Contents->content = htmlspecialchars($request->content, ENT_QUOTES);
        $Contents->description = $request->description;
        $Contents->content_img = $newName;

        if($Contents->save()) {

            /**
             * instantiate TagController and save on tag table
             * then get id everytime its save on $taglist
             */
            $tagCont = new TagController();
            $taglist = $tagCont->createContentTag($request->tag);
            $idcontent = $Contents->id;

            /**
             * loop thru $taglist
             * then instantiate RedisController
             * hset everything on $taglist
             */
            for ($i = 0; $i < count($taglist); $i++) {
                
                $redis = new RedisController();
                echo $taglist[$i][0];
                $redis->contentTag($taglist[$i][0], $idcontent);
            }

            return response()->json([
                "message" => "Contents Saved."
            ]);
        } else {
            return response()->json([
                "message" => "Failed to create new Content."
            ]);
        }
    }

    /**
     * method to save or publish temporarily saved content by client
     * 
     * @param $iduser
     * 
     * @return response
     */
    public function saveTempContent($iduser){
        
        $tempContent = Content::where('iduser', $iduser)
                                ->whereNull('createdate')
                                ->where('delete', 0)
                                ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $tempContent;

        if($cursor->count() > 0 ) {

            $utility = new UtilityController();
            $create_at = $utility->setDatetime();

            $updateContents = Content::where('iduser', $iduser)
                                        ->whereNull('createdate');
                if($updateContents->update([
                                    'createdate'   => $create_at
                                    ])) {
                    return response()->json([
                        "message" => "Content is now publish."
                    ]);
                } else {
                    return response()->json([
                        "message" => "there is nothing to update."
                    ]);
                }
        } else {
            return response()->json([
                "message" => "No list content are found."
            ]);
        }
    }

    /**
     * method to create content 
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function createContent(Request $request){

        //get the image file
        $photo = $request->file('image');

        $utility = new UtilityController();
        $create_at = $utility->setDatetime();
        $newImage = $utility->contentResize($photo);

        //set new name for image to save on database
        $newName = 'assets/content/'.time().'.'.$photo->getClientOriginalExtension(); 

        //set directory to save the file
        $destinationPath = $utility->public_path('/');

        //save to image to public/assets/banner folder
        $newImage->save($destinationPath.'/'.$newName,80);
        
        $Contents = new Content();
        $Contents->iduser = $request->iduser;
        $Contents->title = $request->title;
        $Contents->content = htmlspecialchars($request->content, ENT_QUOTES);
        $Contents->description = $request->description;
        $Contents->content_img = $newName;
        $Contents->createdate = $create_at;

        if($Contents->save()) {

            /**
             * instantiate TagController and save on tag table
             * then get id everytime its save on $taglist
             */
            $tagCont = new TagController();
            $taglist = $tagCont->createContentTag($request->tag);
            $idcontent = $Contents->id;

            /**
             * loop thru $taglist
             * then instantiate RedisController
             * hset everything on $taglist
             */
            for ($i = 0; $i < count($taglist); $i++) {
                
                $redis = new RedisController();
                echo $taglist[$i][0];
                $redis->contentTag($taglist[$i][0], $idcontent);
            }

            return response()->json([
                "message" => "New Contents Created."
            ]);
        } else {
            return response()->json([
                "message" => "Failed to create new Content."
            ]);
        }
    }

    /**
     * method to create content 
     * 
     * @param Request $request $id
     * 
     * @return response
     */
    public function updateContent(Request $request, $idcontent){

        //find content info
        $Contents = Content::where('idcontent', $idcontent)
                            ->where('delete', 0)
                            ->get();

        if($Contents->count() > 0 ) {

            $utility = new UtilityController();
            $modifieddate = $utility->setDatetime();

            //update content
            $updateContents = Content::where('idcontent', $idcontent);
                if($updateContents->update([
                                    'title'         => $request->title,
                                    'content'       => $request->content,
                                    'description'   => $request->description,
                                    'modifieddate'   => $modifieddate
                                    ])) {
                    return response()->json([
                        "message" => "Content Info Updated."
                    ]);
                } else {
                    return response()->json([
                        "message" => "content save failed."
                    ]);
                }
        } else {
            return response()->json([
                "message" => "Content not found."
            ]);
        }        
    }

    /**
     * method to read all content in desc order by number of likes
     * inner join users and likes
     * 
     * @return Responce
     */
    public function newContent(){

        $current = new UtilityController();       

        $contents = DB::table('contents')
                        ->join('users','contents.iduser', '=', 'users.iduser')
                        ->select('contents.idcontent',
                                'contents.iduser',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.content_img',
                                'contents.createdate',
                                'contents.modifieddate'
                        )
                        ->where('contents.delete', 0)
                        ->orderBy('contents.createdate', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $contents;
        
        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcontent      = $new->idcontent;
                $iduser         = $new->iduser;
                $nickname       = $new->nickname;
                $title          = $new->title;
                $content        = $new->content;
                $description    = $new->description;
                $content_img    = $new->content_img;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);

                 /**
                 * get other content info
                 * 
                 * retrieved
                 *  * like count
                 *  * comment count
                 *  * user profile photo
                 */
                $like = new LikeController();
                $countlike = $like->countLike($idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                //now we put all in an array and return
                $array[] = [
                    'idcontent'     => $idcontent,
                    'iduser'        => $iduser,
                    'nickname'      => $nickname,
                    'userphoto'     => $userphoto,
                    'title'         => $title,
                    'content'       => $content,
                    'description'   => $description,
                    'content_img'   => $content_img,
                    'createdate'    => $createdate,
                    'modifieddate'  => $modifieddate,
                    'timelapse'     => $timelapse,
                    'like'          => $countlike,
                    'comment'       => $countComment
                ];

            }
            
            return response()->json($array);
        } else {
            return response()->json([
                "message" => "No Content are found."
            ]);
        }
    }

    /**
     * method to get one content info
     * 
     * @param $id
     * 
     * @return response
     */
    public function contentReadOne($idcontent){

        //create query contents inner joint users
        $content = DB::table('contents')
                        ->join('users', 'contents.iduser', '=', 'users.iduser')               
                        ->select('contents.idcontent', 'contents.iduser', 'users.nickname', 'contents.title', 'contents.content', 'contents.description',
                                'contents.createdate', 'contents.modifieddate', 'contents.delete')
                        ->where('contents.delete', 0)
                        ->where('contents.idcontent', $idcontent)
                        ->get();          
        
        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $content;

        /**
         * take the Date and Time Controller to set current date
         * 
         * then use the timelapse function
         */
        $current = new UtilityController();

        if($cursor->count() > 0 ) {                   
            foreach($cursor as $new) {
                $iduser = $new->iduser;

                //retrived user photo
                $userinfo = DB::connection('mongodb')->collection('userinformations')
                ->select('profilephoto')
                ->where('iduser', '=', $iduser)
                ->get();

                foreach($userinfo as $qwerty){
                    $photo = $qwerty['profilephoto'];
                }

                $content = [
                    'idcontent' => $new->idcontent,
                    'iduser' => $new->iduser,
                    'photo' => $photo,
                    'nickname' => $new->nickname,
                    'title' => $new->title,
                    'content' => $new->content,
                    'description' => $new->description,
                    'createdate' => $new->createdate,
                    'modifieddate' => $new->modifieddate,
                    'timelapse' => $current->timeLapse($new->createdate)
                ];                           
            }

            /**
             * get LikeController Instance
             * 
             * retrieved
             *  * like count
             *  * dislike count
             */
            $like = new LikeController();
            $countlike = $like->countLike($idcontent);
            $countdislike = $like->countDislike($idcontent);

            //append all the data needed to display for content
            $contentData = [
                'content'   => $content,
                'like'      => $countlike,
                'dislike'   => $countdislike
            ];
            return response()->json($contentData);    
        } else {
            return response()->json([
                "message" => "Content not found."
            ]);
        }        
    }

    /**
     * method to load hot content
     * 
     * function use for a content to be on hot list is
     * 72 hours after created
     * comment + (like - dislike)
     * sort desc
     * 
     * @return response
     */
    public function hotContent(){

        $current = new UtilityController();       

        $contents = DB::table('contents')
                        ->join('users','contents.iduser', '=', 'users.iduser')
                        ->select('contents.idcontent',
                                'contents.iduser',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.content_img',
                                'contents.createdate',
                                'contents.modifieddate'
                        )
                        ->where('contents.delete', 0)
                        ->orderBy('contents.createdate', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $contents;
        
        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcontent      = $new->idcontent;
                $iduser         = $new->iduser;
                $nickname       = $new->nickname;
                $title          = $new->title;
                $content        = $new->content;
                $description    = $new->description;
                $content_img    = $new->content_img;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);

                
                 /**
                 * get other content info
                 * 
                 * retrieved
                 *  * like count
                 *  * comment count
                 *  * user profile photo
                 */
                $like = new LikeController();
                $countlike = $like->countLike($idcontent);
                $countdislike = $like->countDislike($idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                $utility = new UtilityController();

                $points = $countComment + ($countlike - $countdislike);

                $hot = $utility->isItHot($createdate, $points);
                
                if($hot){
                    $array[] = [
                        'idcontent'     => $idcontent,
                        'iduser'        => $iduser,
                        'nickname'      => $nickname,
                        'userphoto'     => $userphoto,
                        'title'         => $title,
                        'content'       => $content,
                        'description'   => $description,
                        'content_img'   => $content_img,
                        'createdate'    => $createdate,
                        'modifieddate'  => $modifieddate,
                        'timelapse'     => $timelapse,
                        'like'          => $countlike,
                        'dislike'       => $countdislike,
                        'comment'       => $countComment,
                        'points'        => $points
                    ];
                } else {
                    return response()->json([
                        "message" => "No Content are found."
                    ]);
                }
            }
            return response()->json($array);
        } else {
            return response()->json([
                "message" => "No Content are found."
            ]);
        }
    }

    /**
     * method to load hot content
     * 
     * function use for a content to be on hot list is
     * 72 hours after created
     * comment + (like - dislike)
     * sort desc
     * 
     * @return response
     */
    public function trendingContent(){

        $current = new UtilityController();       

        $contents = DB::table('contents')
                        ->join('users','contents.iduser', '=', 'users.iduser')
                        ->select('contents.idcontent',
                                'contents.iduser',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.content_img',
                                'contents.createdate',
                                'contents.modifieddate'
                        )
                        ->where('contents.delete', 0)
                        ->orderBy('contents.createdate', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $contents;
        
        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcontent      = $new->idcontent;
                $iduser         = $new->iduser;
                $nickname       = $new->nickname;
                $title          = $new->title;
                $content        = $new->content;
                $description    = $new->description;
                $content_img    = $new->content_img;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);

                
                 /**
                 * get other content info
                 * 
                 * retrieved
                 *  * like count
                 *  * comment count
                 *  * user profile photo
                 */
                $like = new LikeController();
                $countlike = $like->countLike($idcontent);
                $countdislike = $like->countDislike($idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                $utility = new UtilityController();

                $points = $countComment + ($countlike - $countdislike);

                $trending = $utility->isItTrending($createdate, $points);
                
                if($trending){
                    $array[] = [
                        'idcontent'     => $idcontent,
                        'iduser'        => $iduser,
                        'nickname'      => $nickname,
                        'userphoto'     => $userphoto,
                        'title'         => $title,
                        'content'       => $content,
                        'description'   => $description,
                        'content_img'   => $content_img,
                        'createdate'    => $createdate,
                        'modifieddate'  => $modifieddate,
                        'timelapse'     => $timelapse,
                        'like'          => $countlike,
                        'dislike'       => $countdislike,
                        'comment'       => $countComment,
                        'points'        => $points
                    ];
                } else {
                    return response()->json([
                        "message" => "No Content are found."
                    ]);
                }
            }
            return response()->json($array);
        } else {
            return response()->json([
                "message" => "No Content are found."
            ]);
        }
    }
}
