<?php

namespace App\Http\Controllers;

use App\User;
use App\UserInfo;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoogleController extends Controller
{
    public $googleUser;
    public $first_name;
    public $last_name;
    public $email;
    public $nickname;
    public $snsProviderId;
    public $photo;
    /**
     * method to call SocialiteProdivers to get Google account authentication
     * 
     * using stateless for we use lumen that does'nt support any session
     * and its stateless
     */  
    public function redirectToProvider(){

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * method to call google callback
     * 
     * get the token bearer for our authorization
     * 
     * @return response
     */
    public function handleProviderCallback(){

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
                "message" => "redirect to login modal"
            ]);
        } 

        //save the data to mysql and mongodb
        $save = new User();
            
        $save->first_name = $user->user['name']['givenName'];
        $save->last_name = $user->user['name']['familyName'];
        $save->email = $user->getEmail();
        $save->nickname = $user->user['displayName'];
        $save->snsProviderName  = 'Google +';
        $save->snsProviderId =$user->getId();

        if($save->save()) {
            
            $userinfo = new UserInfo();
            $userinfo->iduser = $lastId = $save->id;
            $userinfo->profilephoto = $photo;

            if($userinfo->save()){
                return response()->json([
                    "message" => "redirect to index page and ask for new password"
                ]);
            }
        } else {
            return response()->json([
                "message" => "failed to save information"
            ]);
        }
    }    
}
