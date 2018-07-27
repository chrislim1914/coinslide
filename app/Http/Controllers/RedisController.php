<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RedisController extends Controller {

    private $redis;

    /**
     * redis connection
     */
    public function __construct(){
        $this->redis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => getenv('REDIS_DB'),
        ]);
    }

    /**
     * load total reward
     * 
     * @return response
     */
    public function totalreward(){        
        $title = getenv('APP_NAME');
        return response()->json([
            "message" => $this->redis->hget($title,'totalreward')
            ]);     
    }

    /**
     * method to save content tags
     * 
     * @param $tagno, $idcontent
     */
    public function contentTag($tagno, $idcontent){

        $contentno = 'content'.$idcontent;
        $this->redis->hset($tagno, $contentno, $idcontent);
    }

    /**
     * method to save ads tags
     * 
     * @param $tagno, $idads
     */
    public function adsTag($tagno, $idads){

        $adsno = 'ads'.$idads;
        $this->redis->hset($tagno, $adsno, $idads);
    }

    /**
     * method to load all tags
     * 
     * @return response
     */
    public function loadAllTags(){

        $tag = $this->redis->keys('*');

        return response()->json($tag);
    }

    /**
     * load all tags and its content or ads link
     * 
     * @return response
     */
    public function loadAll(){
        $tag = $this->redis->keys('*');

        for ($i = 0; $i < count($tag); $i++) {
            $taglist = $this->redis->hgetall($tag[$i]);
            $tags = $taglist;
            $list[] = [$tag[$i] => $tags];
            print_r(array_keys($tags));
            
        }
        // return response()->json($list);
    }
}

