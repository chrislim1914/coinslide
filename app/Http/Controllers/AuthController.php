<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthController extends Controller
{
     /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {        
        $this->jwt = $jwt;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {

            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'email, password not correct'], 200);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['error creating token' => $e->getMessage()], 500);

        }
        return response()->json([
            'message' => 'login ok',
            'token' => "$token",
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(){
        try{
            return response()->json(Auth::user());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e)
           {
            return response()->json([
                'message' => $e->getMessage(),
                'result'    => false
            ]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(){
        return response()->json(['token' => Auth::refresh()]);
    }

    /**
     * method to login google on mobile
     */
    public function findGoogleProvider(Request $request){

        $snsProviderId = User::where('snsProviderId', $request->snsProviderId)->get();

        if($snsProviderId->count() > 0){
            foreach($snsProviderId as $authuser){
                try { 
                    $user = User::where('snsProviderId', $request->snsProviderId)->first();
                    if(! $token = $this->jwt->fromUser($user)){
                        return response()->json([
                            'message' => 'credential not valid',
                            'result'=> false
                        ]);
                    }
                } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {        
                    return response()->json([
                        'token_invalid' => $e->getMessage(),
                        'result'        => false
                    ]);        
                } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {        
                    return response()->json(['error creating token' => $e->getMessage()], 500);        
                }                
            }

            return response()->json([
                "message"       => "redirect to login.",
                "userdata"      => $user,
                "token"         => $token,
                "result"        => true
            ]);
        }else{
            return response()->json([
                "message" => "account dont exist.",
                "result"  => false
            ]);
        }
    }
}