<?php

namespace App\Http\Controllers;

use App\Contents;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\DateTimeController;

class ContentController extends Controller {

    /**
     * method to read all content in desc order by idcontent
     * inner join users and likes
     * 
     * @return Responce
     */
    public function readAllContent(){

        $current = new DateTimeController();

        $nolike = DB::table('contents')
                        ->join('users','contents.user_id', '=', 'users.iduser')
                        ->select('contents.idcontent',
                                'contents.user_id',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.createdate',
                                'contents.modifieddate',
                                'contents.delete',
                                DB::raw("(1) as `islike`"),
                                DB::raw("(0) as `like`")
                        )
                        ->whereNotIn('contents.idcontent', DB::table('likes')
                                                            ->select('likes.idcontent')
                                    )
                        ->where('contents.delete', 0);

        $withlike = DB::table('contents')
                        ->join('users','contents.user_id', '=', 'users.iduser')
                        ->join('likes', 'contents.idcontent', '=', 'likes.idcontent')
                        ->select('contents.idcontent',
                                'contents.user_id',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.createdate',
                                'contents.modifieddate',
                                'contents.delete',
                                DB::raw("(1) as `islike`"),
                                DB::raw('count(likes.idlike) as `like`')
                        )                        
                        ->where('likes.islike', 1)
                        ->where('contents.delete', 0)
                        ->union($nolike)
                        ->groupBy('contents.idcontent')
                        ->orderBy('like', 'DESC')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $withlike;
        
        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcontent      = $new->idcontent;
                $user_id        = $new->user_id;
                $nickname       = $new->nickname;
                $title          = $new->title;
                $content        = $new->content;
                $description    = $new->description;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);
                $delete         = $new->delete;
                $islike         = $new->islike;
                $like           = $new->like;

                $array[] = [
                    'idcontent'     => $idcontent,
                    'user_id'       => $user_id,
                    'nickname'      => $nickname,
                    'title'         => $title,
                    'content'       => $content,
                    'description'   => $description,
                    'createdate'    => $createdate,
                    'modifieddate'  => $modifieddate,
                    'timelapse'     => $timelapse,
                    'delete'        => $delete,
                    'islike'        => $islike,
                    'like'          => $like
                ];
            }
            
            return response()->json($array);
        } else {
            echo json_encode(
                array("message" => "No Content are found.")
            );
        }
    }

    /**
     * method to read best content in desc order by idcontent
     * inner join users and likes
     * 
     * @return Responce
     */
    public function bestContent(){

        $current = new DateTimeController();

        $nolike = DB::table('contents')
                        ->join('users','contents.user_id', '=', 'users.iduser')
                        ->select('contents.idcontent',
                                'contents.user_id',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.createdate',
                                'contents.modifieddate',
                                'contents.delete',
                                DB::raw("(1) as `islike`"),
                                DB::raw("(0) as `like`")
                        )
                        ->whereNotIn('contents.idcontent', DB::table('likes')
                                                            ->select('likes.idcontent')
                                    )
                        ->where('contents.delete', 0);

        $withlike = DB::table('contents')
                        ->join('users','contents.user_id', '=', 'users.iduser')
                        ->join('likes', 'contents.idcontent', '=', 'likes.idcontent')
                        ->select('contents.idcontent',
                                'contents.user_id',
                                'users.nickname',
                                'contents.title',
                                'contents.content',
                                'contents.description',
                                'contents.createdate',
                                'contents.modifieddate',
                                'contents.delete',
                                DB::raw("(1) as `islike`"),
                                DB::raw('count(likes.idlike) as `like`')
                        )                        
                        ->where('likes.islike', 1)
                        ->where('contents.delete', 0)
                        ->union($nolike)
                        ->groupBy('contents.idcontent')
                        ->orderBy('like', 'DESC')
                        ->limit(8)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $withlike;
        
        /**
         * we need to loop to apply timelapse function
         * 
         * apparently this will output only one set of data instead of its normal procedure
         * so we use var array[] to fill everytime foreach condition passed
         */
        if($cursor->count() > 0 ) {    
            foreach($cursor as $new){
                $idcontent      = $new->idcontent;
                $user_id        = $new->user_id;
                $nickname       = $new->nickname;
                $title          = $new->title;
                $content        = $new->content;
                $description    = $new->description;
                $createdate     = $new->createdate;
                $modifieddate   = $new->modifieddate;
                $timelapse      = $current->timeLapse($new->createdate);
                $delete         = $new->delete;
                $islike         = $new->islike;
                $like           = $new->like;

                $array[] = [
                    'idcontent'     => $idcontent,
                    'user_id'       => $user_id,
                    'nickname'      => $nickname,
                    'title'         => $title,
                    'content'       => $content,
                    'description'   => $description,
                    'createdate'    => $createdate,
                    'modifieddate'  => $modifieddate,
                    'timelapse'     => $timelapse,
                    'delete'        => $delete,
                    'islike'        => $islike,
                    'like'          => $like
                ];
            }
            
            return response()->json($array);
        } else {
            echo json_encode(
                array("message" => "No Content are found.")
            );
        }
    }

    /**
     * methos for search content by 
     */
    public function searchContent(Request $request){
        /**
         * check if search string is null before we query
         */
        if($request->search == null) {
            echo json_encode(
                array("message" => "No Search String.")
            );
        } else {
            $Contents = DB::table('contents')
                            ->join('users', 'contents.user_id', '=', 'users.iduser')
                            ->select('contents.idcontent', 'contents.user_id', 'users.nickname', 'contents.title', 'contents.content', 'contents.description',
                                    'contents.delete')
                            ->Where('users.nickname', 'LIKE', "%$request->search%")
                            ->orWhere('contents.title', 'LIKE', "%$request->search%")
                            ->orWhere('contents.description', 'LIKE', "%$request->search%")                            
                            ->Where('contents.delete', 0)
                            ->paginate(5);

            //the cursor method may be used to greatly reduce your memory usage:
            $cursor = $Contents;

            if($cursor->count() > 0 ) {
                return response()->json($cursor);
            } else {
                echo json_encode(
                    array("message" => "No User are found.")
                );
            }
        }
    }

    /**
     * method to create content 
     * 
     * @return Request $request
     */
    public function createContent(Request $request){

        $Contents = new Contents();
        $Contents->user_id = $request->user_id;
        $Contents->title = $request->title;
        $Contents->content = $request->content;
        $Contents->description = $request->description;

        if($Contents->save()) {
            echo json_encode(
                array("message" => "New Contents Created.")
            );
        } else {
            echo json_encode(
                array("message" => "Contents not created.")
            );
        }
    }

    /**
     * method to create content 
     * 
     * @return Request $request $id
     */
    public function updateContent(Request $request, $id){

        //find content info
        $Contents = Contents::where('idcontent', $id)
                            ->where('delete', 0)
                            ->get();

        if($Contents->count() > 0 ) {
            //update content
            $updateContents = Contents::where('idcontent', $id);
                if($updateContents->update([
                                    'title'         => $request->title,
                                    'content'       => $request->content,
                                    'description'   => $request->description
                                    ])) {
                    echo json_encode(
                        array("message" => "Content Info Updated.")
                    );
                } else {
                    echo json_encode(
                        array("message" => "there is nothing to update.")
                    );
                }
        } else {
            echo json_encode(
                array("message" => "Content not found.")
            );   
        }        
    }

    /**
     * method to soft delete content
     * 
     * @return $id
     */
    public function deleteContent($id){
        //find content info
        $Contents = Contents::where('idcontent', $id)
                            ->where('delete', 0)
                            ->get();

        if($Contents->count() > 0 ) {
            //update content
            $updateContents = Contents::where('idcontent', $id);
                if($updateContents->update([
                                    'delete'         => '1'
                                    ])) {
                    echo json_encode(
                        array("message" => "Content Deleted.")
                    );
                } else {
                    echo json_encode(
                        array("message" => "Content deletion failed.")
                    );
                }
        } else {
            echo json_encode(
                array("message" => "Content not found.")
            );   
        }

    }
    /**
     * method to get one content info
     * 
     * @return $id
     */
    public function contentReadOne($id){

        //create query contents inner joint users
        $content = DB::table('contents')
                        ->join('users', 'contents.user_id', '=', 'users.iduser')               
                        ->select('contents.idcontent', 'contents.user_id', 'users.nickname', 'contents.title', 'contents.content', 'contents.description',
                                'contents.createdate', 'contents.modifieddate', 'contents.delete')
                        ->where('contents.delete', 0)
                        ->where('contents.idcontent', $id)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $content;

        /**
         * take the Date and Time Controller to set current date
         * 
         * then use the timelapse function
         */
        $current = new DateTimeController();

        if($cursor->count() > 0 ) {                   
            foreach($cursor as $new) {
                return response()->json([
                    'idcontent' => $new->idcontent,
                    'iduser' => $new->user_id,
                    'nickname' => $new->nickname,
                    'title' => $new->title,
                    'content' => $new->content,
                    'description' => $new->description,
                    'createdate' => $new->createdate,
                    'modifieddate' => $new->modifieddate,
                    'timelapse' => $current->timeLapse($new->createdate),
                    'delete' => $new->delete,
                ]);                
            }
        } else {
            echo json_encode(
                array("message" => "Content not found.")
            );
        }
        
    }
}
