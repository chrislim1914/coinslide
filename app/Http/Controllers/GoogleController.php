<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class GoogleController extends Controller
{
    public $gClient;

    public function __construct() {
        
       session_start();
        
        $this->googleSetAuthentication();    
    }

    public function googleSetAuthentication() {
        
        $clientid = getenv('GOOGLE_CLIENTID');
        $clientSecret = getenv('GOOGLE_SECRET');
        $appName = getenv('APP_NAME');

        //authenticate via google client
        $this->gClient = new \Google_Client();    
        $this->gClient->setClientId($clientid);
        $this->gClient->setClientSecret($clientSecret);
        $this->gClient->setApplicationName($appName);
        $this->gClient->setRedirectUri("http://localhost:8900/callback/google");
        $this->gClient->addScope("profile https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");
        $this->gClient->createAuthUrl();

        return $this->gClient;
    }

    public function googleCallback() {

        if (isset($_SESSION['access_token']))
        	$gClient->setAccessToken($_SESSION['access_token']);
        else if (isset($_GET['code'])) {
        	$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
        	$_SESSION['access_token'] = $token;
        } else {
        	header('Location: login.php');
        	exit();
        }

        $oAuth = new \Google_Service_Oauth2($gClient);
        $userData = $oAuth->userinfo_v2_me->get();

        $_SESSION['id'] = $userData['id'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['gender'] = $userData['gender'];
        $_SESSION['picture'] = $userData['picture'];
        $_SESSION['familyName'] = $userData['familyName'];
        $_SESSION['givenName'] = $userData['givenName'];

        header('Location: index.php');
        exit();
    }
}
