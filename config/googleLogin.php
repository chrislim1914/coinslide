<?php
	session_start();
    require_once __DIR__.'/../vendor/autoload.php';

    $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
    $dotEnv->load();
    $clientid = getenv('GOOGLE_CLIENTID');
    $clientSecret = getenv('GOOGLE_SECRET');

    $gClient = new Google_Client();    
	$gClient->setClientId($clientid);
	$gClient->setClientSecret($clientSecret);
	$gClient->setApplicationName("coinslide-alpha");
	$gClient->setRedirectUri("http://localhost:8000/config/googleCallback.php");
	$gClient->addScope("profile https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");
