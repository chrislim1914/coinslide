<?php

namespace App\Http\Controllers;

use App\User;
use App\UserInfo;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSdk;

class SnsController extends Controller
{
    /**
     * method to call SocialiteProdivers to get Google account authentication
     * 
     * using stateless for we use lumen that does'nt support any session
     * and its stateless
     */  
    public function redirectToGoogle(){

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * method to call google callback
     * 
     * get the token bearer for our authorization
     * 
     * @return response
     */
    public function googleCallback(){

        $user = Socialite::driver('google')->user(); 

        $token = $user->token;
        $refreshToken = $user->refreshToken; 
        $expiresIn = $user->expiresIn;
        $token = $user->token;

        /**
         * get large profile photo from google+
         * default size is 50px
         * we'll just request for 128px
         */
        $oldsize = rtrim($user->getAvatar(), '50');
        $newsize = $oldsize.'128';        
        $photo = $newsize;

        $users = User::where('email', $user->getEmail())
                        ->get();

        if($users->count() > 0){
            return response()->json([
                "message" => "redirect to login modal",
                'result'    =>  true
            ]);
        } 

        $UserData[] = [
            'first_name'        => $user->user['name']['givenName'],
            'last_name'         => $user->user['name']['familyName'],
            'email'             => $user->getEmail(),
            'nickname'          => $user->user['displayName'],
            'snsProviderName'   => 'Google +',
            'snsProviderId'     => $user->getId(),
            'profilephoto'      => $photo,
            'token'             => $token,
            'refreshToken'      => $refreshToken,
            'expiresIn'         => $expiresIn,
        ];

        return response()->json([
            'data'      => $UserData,
            'result'    =>  true
        ]);
    }

    /**
     * 
     * to be use on mobile App
     * method to call SocialiteProdivers to get Google account authentication
     * 
     * using stateless for we use lumen that does'nt support any session
     * and its stateless
     */  
    public function redirectToGoogleMobile(){

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * method to call google callback
     * 
     * get the token bearer for our authorization
     * 
     * @return response
     */
    public function googleCallbackMobile(){

        $user = Socialite::driver('google')->stateless()->user(); 

        $token = $user->token;
        $refreshToken = $user->refreshToken; 
        $expiresIn = $user->expiresIn;

        /**
         * get large profile photo from google+
         * default size is 50px
         * we'll just request for 128px
         */
        $oldsize = rtrim($user->getAvatar(), '50');
        $newsize = $oldsize.'128';        
        $photo = $newsize;

        $users = User::where('email', $user->getEmail())
                        ->where('snsProviderName', $user->getId())
                        ->get();

        if($users->count() > 0){
            return response()->json([
                "message" => "redirect to login modal",
                'result'    =>  true
            ]);
        } 

        $UserData[] = [
            'first_name'        => $user->user['name']['givenName'],
            'last_name'         => $user->user['name']['familyName'],
            'email'             => $user->getEmail(),
            'nickname'          => $user->user['displayName'],
            'snsProviderName'   => 'Google +',
            'snsProviderId'     => $user->getId(),
            'profilephoto'      => $photo,
            'token'             => $token,
            'refreshToken'      => $refreshToken,
            'expiresIn'         => $expiresIn,
        ];

        return response()->json([
            'data'      => $UserData,
            'result'    =>  true
        ]);
    }
    
    public function redirectToFacebook(LaravelFacebookSdk $fb){
        $login_link = $fb
            ->getRedirectLoginHelper()
            ->getLoginUrl(['https://api.coinslide.io/facebook/callback/'], ['email']);
    
    		return '<a href="' . $login_link . '">Log in with Facebook</a>';
    }

    public function facebookCallback(LaravelFacebookSdk $fb){
        // Obtain an access token.
		try {
			$token = $fb->getAccessTokenFromRedirect();
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
		}

		// Access token will be null if the user denied the request
		// or if someone just hit this URL outside of the OAuth flow.
		if (! $token) {
			// Get the redirect helper
			$helper = $fb->getRedirectLoginHelper();

			if (! $helper->getError()) {
				abort(403, 'Unauthorized action.');
			}

			// User denied the request
			dd(
				$helper->getError(),
				$helper->getErrorCode(),
				$helper->getErrorReason(),
				$helper->getErrorDescription()
			);
		}

		if (! $token->isLongLived()) {
			// OAuth 2.0 client handler
			$oauth_client = $fb->getOAuth2Client();

			// Extend the access token.
			try {
				$token = $oauth_client->getLongLivedAccessToken($token);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				dd($e->getMessage());
			}
		}

		$fb->setDefaultAccessToken($token);

		// Save for later
		Session::put('fb_user_access_token', (string) $token);

		// Get basic info on the user from Facebook.
		try {
			$response = $fb->get('/me?fields=id,name,email');
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
		}

		// Convert the response to a `Facebook/GraphNodes/GraphUser` collection
		$facebook_user = $response->getGraphUser();

		// Create the user if it does not exist or update the existing entry.
		// This will only work if you've added the SyncableGraphNodeTrait to your User model.
		$user = App\User::createOrUpdateGraphNode($facebook_user);

		// Log the user into Laravel
		Auth::login($user);

		return response()->json(['message', 'Successfully logged in with Facebook']);
	}
}
