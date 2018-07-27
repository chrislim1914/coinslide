<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * method to read all tags
     * 
     * @return responce
     */
    public function readTags(){

        $tags = Tag::get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $tags;

        if($cursor->count() > 0 ){
            return response()->json($cursor);
        }else {
            return response()->json([
                "message" => "no tags are found."
            ]);
        }
    }

    /**
     * method to save tag name to be use on content and ads create
     * 
     * @param $tag
     * 
     * @return Bool
     */
    public function createTag($tag){

        $taglist = str_word_count($tag, 1);       

        foreach($taglist as $list){
            $newTag = new Tag();
            $newTag->name = $list;
            $newTag->save();
            $idlist[] = [$newTag->name]; 
        }
        return $idlist;
        // if($newTag){
        //     return true;
        // } else {
        //     return false;
        // }
    }
}
