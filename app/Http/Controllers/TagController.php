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
     * method to save tag name to be use on content and ads create
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
}
