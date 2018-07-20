<?php

namespace App\Http\Controllers;

use App\UserInfo;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\DB;

class UserinfoController extends Controller {

    /**
     * method to retrieved all userinformations collection data
     * 
     * @return response
     */
    public function all(){
        $userinfo = UserInfo::all();

        return response()->json($userinfo);
    }

    /**
     * method to update Userinformations collection
     * 
     * @param Request $request $id
     * 
     * @return response
     */
    public function updateUserInfo(Request $request){        
        
        $iduser = intval($request->iduser);
        //check first if user exist
        $userinfo = UserInfo::where('iduser', $iduser)
                            ->get();

        if($userinfo->count() > 0){
            //get the image file
            $photo = $request->file('image');

            //instanciate ImageController for resize
            $resize = new ImageController();
            $newImage = $resize->profilephotoResize($photo);

            //set new name for image to save on database
            $newName = 'assets/profile/'.time().'.'.$photo->getClientOriginalExtension(); 

            //set directory to save the file
            $destinationPath = $resize->public_path('/');        
            
            //save to image to public/assets/banner folder
            $newImage->save($destinationPath.'/'.$newName,80);
            
            //update userinformation with parameter
            $saveUser = DB::connection('mongodb')->collection('userinformations')
                        ->where('iduser', '=', $iduser);

            //execute update
            if($saveUser->update([
                            'gender'       => $request->gender,
                            'profilephoto' => $newName,
                            'birth'        => $request->birth,
                            'city'         => $request->city,
                            'mStatus'      => $request->mStatus
                        ])) {
                return response()->json([
                    "message" => "User information saved!"
                ]);
            } else {
                return response()->json([
                    "message" => "failed to save user information!"
                ]);
            }
        } else {
            return response()->json([
                "message" => "User Information not found."
            ]);
        }
    }
}
