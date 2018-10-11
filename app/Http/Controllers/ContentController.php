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
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

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


        if($request->file('image')==null){

            $Contents = new Content();
            $Contents->iduser = $request->iduser;
            $Contents->title = $request->title;
            $Contents->content = htmlspecialchars($request->content, ENT_QUOTES);
            $Contents->description = $request->description;
    
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
                    $redis->contentTag($taglist[$i][0], $idcontent);
                }
    
                return response()->json([
                    'message' => '',
                    'result'  => true
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to create new Content.',
                    'result'  => false
                ]);
            }

        }
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
                $redis->contentTag($taglist[$i][0], $idcontent);
            }

            return response()->json([
                'message' => '',
                'result'  => true
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to create new Content.',
                'result'  => false
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

            $updateContents = Content::where('iduser', $iduser)->whereNull('createdate');
            
                if($updateContents->update([
                                    'createdate'   => $create_at
                                    ])) {
                    
                    /**
                     * instantiate TagController and save on tag table
                     * then get id everytime its save on $taglist
                     */
                    $tagCont = new TagController();
                    $taglist = $tagCont->createContentTag($request->tag);
                    $idcontent = $Contents->id;

                    $redis = new RedisController();
                    $delTag = $redis->deleteContentTag($idcontent);

                    /**
                     * loop thru $taglist
                     * then instantiate RedisController
                     * hset everything on $taglist
                     */
                    for ($i = 0; $i < count($taglist); $i++) {
                        $redis->contentTag($taglist[$i][0], $idcontent);
                    }
                    return response()->json([
                        'message' => '',
                        'result'  => true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'there is nothing to update.',
                        'result'  => false
                    ]);
                }
        } else {
            return response()->json([
                'message' => 'No list content are found.',
                'result'  => false
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

        $utility = new UtilityController();
        $create_at = $utility->setDatetime();

        if($request->file('image')==null){

            $Contents = new Content();
            $Contents->iduser = $request->iduser;
            $Contents->title = $request->title;
            $Contents->content = htmlspecialchars($request->content, ENT_QUOTES);
            $Contents->description = $request->description;
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
                    $redis->contentTag($taglist[$i][0], $idcontent);
                }
    
                return response()->json([
                    'message' => '',
                    'result'  => true
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to create new Content.',
                    'result'  => false
                ]);
            }
        }

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
                $redis->contentTag($taglist[$i][0], $idcontent);
            }

            return response()->json([
                'message' => '',
                'result'  => true
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to create new Content.',
                'result'  => false
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

        $utility = new UtilityController();
        $modifieddate = $utility->setDatetime();

        //find content info
        $Contents = Content::where('idcontent', $idcontent)
                            ->where('delete', 0)
                            ->get();

        if($Contents->count() > 0 ) {            
            //check if image have content
            if($request->file('image')==null){
                $updateContents = Content::where('idcontent', $idcontent);
                if($updateContents->update([
                                    'title'         => $request->title,
                                    'content'       => $request->content,
                                    'description'   => $request->description,
                                    'modifieddate'   => $modifieddate
                                    ])) {                   
                    
                    /**
                     * instantiate TagController and save on tag table
                     * then get id everytime its save on $taglist
                     */
                    $tagCont = new TagController();
                    $taglist = $tagCont->createContentTag($request->tag);

                    $redis = new RedisController();
                    $delTag = $redis->deleteContentTag($idcontent);

                    /**
                     * loop thru $taglist
                     * then instantiate RedisController
                     * hset everything on $taglist
                     */
                    for ($i = 0; $i < count($taglist); $i++) {
                        $redis->contentTag($taglist[$i][0], $idcontent);
                    }
                    return response()->json([
                        'message' => '',
                        'result'  => true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'content save failed.',
                        'result'  => false
                    ]);
                }
                
            }else{
                //update content
                $photo = $request->file('image');
                $utility = new UtilityController();
                $newImage = $utility->contentResize($photo);

                //set new name for image to save on database
                $image = 'assets/content/'.time().'.'.$photo->getClientOriginalExtension(); 

                //set directory to save the file
                $destinationPath = $utility->public_path('/');           
                $updateContents = Content::where('idcontent', $idcontent);
                if($updateContents->update([
                                    'title'         => $request->title,
                                    'content'       => $request->content,
                                    'description'   => $request->description,
                                    'content_img'   => $image,
                                    'modifieddate'   => $modifieddate
                                    ])) {
                    //save to image to public/assets/banner folder
                    $newImage->save($destinationPath.'/'.$image,80);
                    
                    /**
                     * instantiate TagController and save on tag table
                     * then get id everytime its save on $taglist
                     */
                    $tagCont = new TagController();
                    $taglist = $tagCont->createContentTag($request->tag);

                    $redis = new RedisController();
                    $delTag = $redis->deleteContentTag($idcontent);

                    /**
                     * loop thru $taglist
                     * then instantiate RedisController
                     * hset everything on $taglist
                     */
                    for ($i = 0; $i < count($taglist); $i++) {
                        $redis->contentTag($taglist[$i][0], $idcontent);
                    }
                    return response()->json([
                        'message' => '',
                        'result'  => true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'content save failed.',
                        'result'  => false
                    ]);
                }
            }
        } else {
            return response()->json([
                'message' => 'Content not found.',
                'result'  => false
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
    public function contentReadOne(Request $request, $idcontent){        

        $currentiduser = $this->getHeaderToken($request->header('Authorization'));


        //create query contents inner joint users
        $content = DB::table('contents')
                        ->join('users', 'contents.iduser', '=', 'users.iduser')               
                        ->select('contents.idcontent', 'contents.iduser', 'users.nickname', 'contents.title', 'contents.content', 'contents.description',
                                'contents.content_img', 'contents.createdate', 'contents.modifieddate', 'contents.delete')
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

                $redistag = new RedisController();
                $tag = $redistag->loadContentTag($idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $content = [
                    'idcontent' => $new->idcontent,
                    'iduser' => $new->iduser,
                    'userphoto' => $photo,
                    'content_img' => $new->content_img,
                    'nickname' => $new->nickname,
                    'title' => $new->title,
                    'content' => $new->content,
                    'description' => $new->description,
                    'createdate' => $new->createdate,
                    'modifieddate' => $new->modifieddate,
                    'timelapse' => $current->timeLapse($new->createdate),                    
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
            $count = $redistag->contentViewCount($idcontent);
            $islike = $like->isLike($currentiduser, $idcontent);
            $isdislike = $like->isDislike($currentiduser, $idcontent);
            if($islike){
                $activity = 'liked';
            }elseif($isdislike){
                $activity = 'disliked';
            }else{
                $activity = null;
            }

            //append all the data needed to display for content
            $contentData = [
                'content'       => $content,
                'like'          => $countlike,
                'dislike'       => $countdislike,
                'tag'           => $tag,
                'commentCount'  => $countComment,
                'viewcount'     => $count,
                'useractivity'  => $activity
            ];
            
            return response()->json([
                'data'      => $contentData,
                'result'    => true
            ]); 
        } else {
            return response()->json([
                'message' => 'No Content are found.',
                'result'    => false
            ]);
        }        
    }

    /**
     * method to read all content in desc order by number of likes
     * inner join users and likes
     * 
     * @return Responce
     */
    public function newContent(Request $request){

        $currentiduser = $this->getHeaderToken($request->header('Authorization'));

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
                $islike = $like->isLike($currentiduser, $idcontent);
                $isdislike = $like->isDislike($currentiduser, $idcontent);

                if($islike){
                    $activity = 'liked';
                }elseif($isdislike){
                    $activity = 'disliked';
                }else{
                    $activity = null;
                }

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                $redistag = new RedisController();
                $tag = $redistag->loadContentTag($idcontent);
                $count = $redistag->contentViewCount($idcontent);

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
                    'comment'       => $countComment,
                    'viewcount'     => $count,
                    'tag'           => $tag,
                    'useractivity'  => $activity
                ];

            }
            
            return response()->json([
                'data'      => $array,
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message' => 'No Content are found.',
                'result'    => false
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
    public function hotContent(Request $request){

        $currentiduser = $this->getHeaderToken($request->header('Authorization'));

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
                 *  * tags
                 */
                $like = new LikeController();
                $countlike = $like->countLike($idcontent);
                $countdislike = $like->countDislike($idcontent);
                $islike = $like->isLike($currentiduser, $idcontent);
                $isdislike = $like->isDislike($currentiduser, $idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                $utility = new UtilityController();

                $points = $countComment + ($countlike - $countdislike);

                $hot = $utility->isItHot($createdate, $points);

                $redistag = new RedisController();
                $tag = $redistag->loadContentTag($idcontent);
                $count = $redistag->contentViewCount($idcontent);

                
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
                        'points'        => $points,
                        'viewcount'     => $count,
                        'tag'           => $tag,
                        'useractivity'  => $activity
                    ];
                } else {
                    return response()->json([
                        'message' => 'No Content are found.',
                        'result'    => false
                    ]);
                }
            }
            return response()->json([
                'data'      => $array,
                'result'    => true
            ]);
        } else {
            return response()->json([
                'message' => 'No Content are found.',
                'result'    => false
            ]);
        }
    }

    /**
     * method to load trending content
     * 
     * function use for a content to be on trending list is
     * 360 hours after created
     * comment + (like - dislike)
     * sort desc
     * 
     * @return response
     */
    public function trendingContent(Request $request){

        $currentiduser = $this->getHeaderToken($request->header('Authorization'));

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
                 *  * tags
                 */
                $like = new LikeController();
                $countlike = $like->countLike($idcontent);
                $countdislike = $like->countDislike($idcontent);
                $islike = $like->isLike($currentiduser, $idcontent);
                $isdislike = $like->isDislike($currentiduser, $idcontent);

                $comment = new CommentController();
                $countComment = $comment->countCommentforContentList($idcontent);

                $user = new UserInfoController();
                $userphoto = $user->getUserPhoto($iduser);

                $utility = new UtilityController();

                $points = $countComment + ($countlike - $countdislike);

                $trending = $utility->isItTrending($createdate, $points);

                $redistag = new RedisController();
                $tag = $redistag->loadContentTag($idcontent);
                $count = $redistag->contentViewCount($idcontent);
                
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
                        'points'        => $points,
                        'viewcount'     => $count,
                        'tag'           => $tag,
                        'useractivity'  => $activity
                    ];
                } else {
                    return response()->json([
                        'message'   => 'No Content are found.',
                        'result'    => true
                    ]);
                }
            }
            return response()->json($array);
        } else {
            return response()->json([
                'message'   => 'No Content are found.',
                'result'    => false
            ]);
        }
    }

    public function deleteContent($idcontent){

        //check first if exist
        $check = Content::where('idcontent', $idcontent)->get();

        if($check->count() > 0){

            $delete = Content::where('idcontent', $idcontent);
            if($delete->update([
                'delete'    => 1
            ])){
                return response()->json([
                    'message'  =>  '',
                    'result'  =>  true
                ]);
            }else{
                return response()->json([
                    'message'  =>  'Failed to delete content',
                    'result'  =>  false
                ]);
            }   
        }else{
            return response()->json([
                'message'  =>  'content not found!',
                'result'  =>  false
            ]);
        }
    }

    public function getHeaderToken($token){

        $header = substr($token, 7);   

        try {

            $token = JWTAuth::setToken((string)$header);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json([
                'message'   => 'token_invalid',
                'result'    => false
                ]);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {            
            return response()->json([
                'message'   => $e->getMessage(),
                'result'    => false
                ]);
        }

        return $currentiduser = $token->getPayload()->get('sub');
    }
}
