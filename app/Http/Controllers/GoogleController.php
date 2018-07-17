<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
   
    public function redirectToProvider()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();   
        print_r($user);     
        // $user->token;
        // OAuth Two Providers
        $token = $user->token;
        $refreshToken = $user->refreshToken; // not always provided
        $expiresIn = $user->expiresIn;

        // OAuth One Providers
        $token = $user->token;

        // All Providers
        echo $user->getId(). '<br />';
        echo $user->getNickname(). '<br />';
        echo $user->getName(). '<br />';
        echo $user->getEmail(). '<br />';
        echo $user->getAvatar(). '<br />';
    }
}
