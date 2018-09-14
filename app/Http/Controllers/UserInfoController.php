<?php

namespace App\Http\Controllers;

use App\UserInfo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;

class UserInfoController extends Controller
{
    /**
     * method to update userinformations collections
     * to be utilized under UserController to update Client info
     * 
     * @param $iduser Array $userdata
     * 
     * @return Bool 
     */
    public function updateUserInfo($iduser, Array $userdata){
        
        $iduser = intval($iduser);
        
        //update userinformation with parameter
        $saveUser = DB::connection('mongodb')->collection('userinformations')
                    ->where('iduser', '=', $iduser);

        //execute update
        if($saveUser->update([
                    'gender'       => $userdata['gender'],
                    'birth'        => $userdata['birth'],
                    'country'      => $userdata['country'],
                    'city'         => $userdata['city'],
                    'mStatus'      => $userdata['mStatus']
                ])) {
            return true;
        } else {
            return false;
        }
    }

    public function updateProfilePhoto(Request $request, $iduser){

        $iduser = intval($iduser);

        //check first if user exist
        $userinfo = UserInfo::where('iduser', $iduser)
                            ->get();

        if($userinfo->count() > 0){
            //get the image file
            $photo = $request->file('image');

            //instanciate ImageController for resize
            $resize = new UtilityController();
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
                            'profilephoto' => $newName,
                        ])) {
                return response()->json([
                    "message" => "Profile photo updated"
                ]);
            }
        } else {
            return response()->json([
                "message" => "client not found."
            ]);
        }
    }

    /**
     * method to retrieved user profile photo
     * 
     * @param $iduser
     * 
     * @return $userinfo
     */
    public function getUserPhoto($iduser){
        //convert the $id to integer
        $iduser = intval($iduser);

        $userinfo = DB::connection('mongodb')->collection('userinformations')
                        ->project(['_id' => 0])
                        ->select('profilephoto')
                        ->where('iduser', '=', $iduser)
                        ->get();
        if($userinfo) {
            foreach($userinfo as $new){
                return $new;
            }
            
        } else {
            return 'no photo';
        }
        
    }
}
