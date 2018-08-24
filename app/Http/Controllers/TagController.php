<?php

namespace App\Http\Controllers;

use App\TagContent;
use App\TagAds;
use App\TagAdvertiser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{   
    /**
     * method to save tag name to be use on content
     * 
     * @param $tag
     * 
     * @return Bool
     */
    public function createContentTag($tag){

        $taglist = str_word_count($tag, 1, '0123456789- ');       

        foreach($taglist as $list){
            $TagContent = new TagContent();
            $TagContent->contant_tag_name = $list;
            $TagContent->save();
            $TagContentlist[] = [$TagContent->contant_tag_name]; 
        }
        return $TagContentlist;
    }

    /**
     * method to load all content tag
     * 
     * @return response
     */
    public function loadAllContentTag(){
        $contentTag = DB::table('tag_contents')
                        ->select('contant_tag_name')
                        ->groupby('contant_tag_name')
                        ->orderBy('contant_tag_name', 'asc')
                        ->get();
        
        if($contentTag->count() > 0){
            return response()->json($contentTag);
        }else{
            return response()->json([
                'message'   => 'no tag found'
            ]);
        }
    }

    /**
     * method to save tag name to be use on ads
     * 
     * @param $tag
     * 
     * @return Bool
     */
    public function createAdsTag($tag){

        $taglist = str_word_count($tag, 1, '0123456789- ');       

        foreach($taglist as $list){
            $TagContent = new TagAds();
            $TagContent->ads_tag_name = $list;
            $TagContent->save();
            $TagContentlist[] = [$TagContent->ads_tag_name]; 
        }
        return $TagContentlist;
    }
}
