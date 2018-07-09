<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class GoogleController extends Controller
{
    public function googleLogin(Request $request)  {

        $clientid = getenv('GOOGLE_CLIENTID');
        $clientSecret = getenv('GOOGLE_SECRET');
        $appName = getenv('APP_NAME');
        $appkey = getenv('GOOGLE_APIKEY');

        $google_redirect_url = route('glogin');

        $gClient = new \Google_Client();
        $gClient->setApplicationName($appName);
        $gClient->setClientId($clientid);
        $gClient->setClientSecret($clientSecret);
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey($appkey);
        $gClient->setScopes(array(
            'https://www.googleapis.com/auth/plus.login',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));

        $google_oauthV2 = new \Google_Service_Oauth2($gClient);

        if ($request->get('code')){
            $gClient->authenticate($request->get('code'));
            $request->session()->put('token', $gClient->getAccessToken());
        }
        if ($request->session()->get('token'))
        {
            $gClient->setAccessToken($request->session()->get('token'));
        }
        if ($gClient->getAccessToken())
        {
            //For logged in user, get details from google using access token
            $guser = $google_oauthV2->userinfo->get();  
               
                $request->session()->put('name', $guser['name']);
                if ($user =User::where('email',$guser['email'])->first())
                {
                    //logged your user via auth login
                }else{
                    //register your user with response data
                }               
         return redirect()->route('user.glist');          
        } else
        {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }
    public function listGoogleUser(Request $request){
      $users = User::orderBy('id','DESC')->paginate(5);
     return view('users.list',compact('users'))->with('i', ($request->input('page', 1) - 1) * 5);;
    }
}
