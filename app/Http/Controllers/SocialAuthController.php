<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;
use Google_Service_People;

class SocialAuthController extends Controller
{
    public function redirectFB()
    {
        return Socialite::driver('facebook')->redirect();   
    }   

    public function callbackFB(SocialAccountService $service)
    {
        // when facebook call us a with token   
        $user = $service->createOrGetUser(Socialite::driver('facebook')->user());

        auth()->login($user);

        return redirect()->to('/home');
    }

    public function redirectG()
    {
        return Socialite::driver('google')
                        ->scopes(['openid', 'profile', 'email', Google_Service_People::CONTACTS_READONLY])
                        ->redirect();
    }
    public function callbackG(Request $request)
    {
        $user = Socialite::driver('google')->user();

        // Set token for the Google API PHP Client
        $google_client_token = [
            'access_token' => $user->token,
            'refresh_token' => $user->refreshToken,
            'expires_in' => $user->expiresIn
        ];
    
        $client = new Google_Client();
        $this->gClient->setClientId('1093020262062-o2smndg4hi9v9h0cmfnqevptnr0smttc.apps.googleusercontent.com');
        $this->gClient->setClientSecret('pEy-mgCG4Zq68vo1dfJr08ql');
        $this->gClient->setApplicationName('CoinSlide');
    
        $service = new Google_Service_People($client);
    
        $optParams = array('requestMask.includeField' => 'person.phone_numbers,person.names,person.email_addresses');
        $results = $service->people_connections->listPeopleConnections('people/me',$optParams);
    
        dd($results);
    }
}