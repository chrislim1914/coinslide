<?php

require_once __DIR__.'/../vendor/autoload.php';

class googleLogin {

    public $gClient;

    public function __construct() {
        
       session_start();
        
        $this->googleSetAuthentication();    
    }

    public function googleSetAuthentication() {

        //load env data
        $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
        $dotEnv->load();
        $clientid = getenv('GOOGLE_CLIENTID');
        $clientSecret = getenv('GOOGLE_SECRET');
        $appName = getenv('APP_NAME');

        //authenticate via google client
        $this->gClient = new Google_Client();    
        $this->gClient->setClientId($clientid);
        $this->gClient->setClientSecret($clientSecret);
        $this->gClient->setApplicationName($appName);
        $this->gClient->setRedirectUri("http://localhost:8900/config/GoogleCallback.php");
        $this->gClient->addScope("profile https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");
        $this->gClient->createAuthUrl();

        return $this->gClient;
    }
}
