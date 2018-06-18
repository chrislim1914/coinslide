<?php

require_once __DIR__.'/../vendor/autoload.php';

class DatabaseRedis {


    public function __construct() {
        
        $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
        $dotEnv->load();
        
        $conn_string = array(
            'schema' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'), 
            'password' => getenv('REDIS_PASS'),
            'database' => getenv('REDIS_DB')
        );

        try {
            $redis_conn = new Predis\Client($conn_string);
            echo $redis_conn->ping();
        } catch(ConnectionException $e) {
            echo 'Connection Error: ' .$e;
        }
    }

}


