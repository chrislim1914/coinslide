<?php

namespace App\Http\Controllers;

use App\TagContent;
use App\TagAds;
use App\TagAdvertiser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * method to read all Content tags
     * 
     * @return responce
     */
    public function readAllContentTags(){

        $tags = TagContent::groupBy('contant_tag_name')
                            ->get();

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
     * method to read all Ads tags
     * 
     * @return responce
     */
    public function readAllAdsTags(){

        $tags = TagAds::get();

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
     * method to read all Advertiser tags
     * 
     * @return responce
     */
    public function readAllAdvertiserTags(){

        $tags = TagAdvertiser::get();

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
    public function createContentTag($tag){

        $taglist = str_word_count($tag, 1);       

        foreach($taglist as $list){
            $TagContent = new TagContent();
            $TagContent->contant_tag_name = $list;
            $TagContent->save();
            $TagContentlist[] = [$TagContent->contant_tag_name]; 
        }
        return $TagContentlist;
    }
}
