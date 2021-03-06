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
            $TagContent->content_tag_name = $list;
            $TagContent->save();
            $TagContentlist[] = [$TagContent->content_tag_name]; 
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
                        ->select('content_tag_name')
                        ->groupby('content_tag_name')
                        ->orderBy('content_tag_name', 'asc')
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

    /**
     * method to load all ads tag
     * 
     * @return response
     */
    public function loadAlladsTag(){
        $contentTag = DB::table('tag_ads')
                        ->select('ads_tag_name')
                        ->groupby('ads_tag_name')
                        ->orderBy('ads_tag_name', 'asc')
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
     * method to save tag name to be use on advertiser
     * 
     * @param $tag
     * 
     * @return Bool
     */
    public function createAdvertiserTag($tag){

        $taglist = str_word_count($tag, 1, '0123456789- ');       

        foreach($taglist as $list){
            $TagContent = new TagAdvertiser();
            $TagContent->advertiser_tag_name = $list;
            $TagContent->save();
            $TagContentlist[] = [$TagContent->advertiser_tag_name]; 
        }
        return $TagContentlist;
    }

    /**
     * method to load all advertiser tag
     * 
     * @return response
     */
    public function loadAlladvertiserTag(){
        $contentTag = DB::table('tag_advertisers')
                        ->select('advertiser_tag_name')
                        ->groupby('advertiser_tag_name')
                        ->orderBy('advertiser_tag_name', 'asc')
                        ->get();
        
        if($contentTag->count() > 0){
            return response()->json($contentTag);
        }else{
            return response()->json([
                'message'   => 'no tag found'
            ]);
        }
    }

    
}
