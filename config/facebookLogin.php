<?php
        
    require_once __DIR__.'/../vendor/autoload.php';

    $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
    $dotEnv->load();

	$FB = new \Facebook\Facebook([
		'app_id' => getenv('FACEBOOK_CLIENTID'),
		'app_secret' => getenv('FACEBOOK_SECRET'),
		'default_graph_version' => 'v3.0'
	]);

	$helper = $FB->getRedirectLoginHelper();