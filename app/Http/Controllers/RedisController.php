<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RedisController extends Controller {

    
    private $trredis;
    private $contentredis;
    private $adsredis;
    private $advertiserredis;
    /**
     * load total reward
     * 
     * @return response
     */
    public function totalreward(){

        /**
         * connection only for total reward
         * 
         * we create separate connection for it use different database inside redis
         * as its has different hset format
         */
        $this->trredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => getenv('REDIS_DB'),
        ]);    

        $title = getenv('APP_NAME');
        return response()->json([
            "message" => $this->trredis->hget($title,'totalreward')
            ]);     
    }

    /**
     * method to save content tags
     * 
     * @param $tagno, $idcontent
     */
    public function contentTag($tagno, $idcontent){

        $this->contentredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 2,
        ]);

        $this->contentredis->lpush($idcontent, $tagno);
    }

    /**
     * method to load tag by content
     */
    public function loadContentTag($idcontent){

        $start = 0;
        $end = -1;

        $this->contentredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 2,
        ]);

        return $this->contentredis->lrange($idcontent, $start, $end);
    }

    /**
     * method to delete tag
     */
    public function deleteContentTag($idcontent){

        $this->contentredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 2,
        ]);

        $this->contentredis->del($idcontent);
    }
    
    /**
     * method to save ads tags
     * 
     * @param $tagno, $idads
     */
    public function adsTag($tagno, $idads){
        $this->adsredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 3,
        ]);

        $this->adsredis->lpush($idads, $tagno);
    }

    public function loadAdsTag($idads){

        $start = 0;
        $end = -1;

        $this->adsredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 3,
        ]);

        return $this->adsredis->lrange($idads, $start, $end);
    }

    /**
     * method to delete tag
     */
    public function deleteAdsTag($idads){

        $this->adsredis = new \Predis\Client([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASS'),
            'database' => 3,
        ]);

        $this->adsredis->del($idcontent);
    }
}

