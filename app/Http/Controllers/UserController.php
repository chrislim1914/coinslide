<?php

namespace App\Http\Controllers;

use App\User;
use App\UserInfo;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;


class UserController extends Controller
{
    /**
     * method to retrieved single user by iduser
     * 
     * @param $id
     * @return Response
     */    
    public function getUser($id){

        //convert the $id to integer
        $iduser = intval($id);

        $userinfo = DB::connection('mongodb')->collection('userinformations')
                        ->where('iduser', '=', $iduser)
                        ->get();

        $user = DB::table('users')
                ->select('users.iduser',
                        'users.first_name',
                        'users.last_name',
                        'users.email',
                        'users.phone',
                        'users.nickname',
                        'users.createdate',
                        'users.delete')
                ->where('users.iduser', $id)
                ->get();

        // $collection = $user->merge($userinfo);  
        $collection = ['primary data' => $user, 'other data' => $userinfo ];

        if($user->count() > 0){
            return response()->json($collection);
        } else {
            return response()->json([
                "message" => "User dont exist!",
                "result"  => false
            ]);
        }
        
    }

    /**
     * method to create a user, validate email and encrypt password
     * 
     * @param  Request  $request
     */
    
    public function createUser(Request $request){

        //check if email already registered
        $Users  = User::where('email', $request->email)->get();
        if($Users->count() > 0){
            return response()->json([
                "message" => "User already exist!",
                "result"  => false
            ]);
        }   

        $hash = new UtilityController();        
        $User = new User();
        // $User->first_name = $request->first_name;
        // $User->last_name = $request->last_name;
        $User->phone = $request->phone;
        $User->email = $request->email;
        $User->nickname = $request->nickname;
        $User->password = $hash->hash($request->password);//password hash
        
        if($User->save()) {
            //retrieved the new inserted iduser
            $lastid = $User->id;

            //save it to userinfo collections
            $userinfo = new UserInfo();
            $userinfo->iduser       = $lastid;
            $userinfo->gender       = $request->gender;
            $userinfo->profilephoto = '';
            $userinfo->birth        = $request->birthdate;
            $userinfo->country      = $request->country;
            $userinfo->city         = $request->city;
            $userinfo->mStatus      = $request->maritalstatus;
            
            $userinfo->save();

            return response()->json([
                "message" => "Save Success!",
                "result"  => true
            ]);
        } else {
            return response()->json([
                "message" => "failed to save!",
                "result"  => false
            ]);
        }  
    }

    /**
     * method update user data excluding password
     * 
     * @param  Request  $request $id
     * 
     * @return response
     */
    public function updateData(Request $request, $id){

        /*
         * find first the user if exist
         */
        $Users  = User::all()
                        ->where('iduser', $id)
                        ->where('delete', 0);        

        if($Users->count() <= 0 ) {
            return response()->json([
                "message" => "User not found.",
                "result"  => false
            ]);
        }

        //instantiate UserInfo Controller
        $userinfo = new UserInfoController();

        //save the userinformations collection data to array
        $userdata = [
            'gender'    => $request->gender,
            'birth'     => $request->birthdate,
            'country'   => $request->country,
            'city'      => $request->city,
            'mStatus'   => $request->maritalstatus,
        ];        
        
        //update users
        $updateUser = DB::table('users')
        ->where('iduser', $id);

        if($updateUser->update([
            // 'first_name'    => $request->first_name,
            // 'last_name'     => $request->last_name,
            'nickname'      => $request->nickname,
                    ])) 
        {
            //update userinformations
            $userinfo->updateUserInfo($id,$userdata);
            
            return response()->json([
                "message" => "User information updated."
                ]);
        } else {
            //update userinformations
            if($userinfo->updateUserInfo($id,$userdata)){
                return response()->json([
                    "message" => "User information updated.",
                    "result"  => true
                    ]);
            } else {
                return response()->json([
                    "message" => "there is nothing to updated.",
                    "result"  => false
                    ]);
            }
            /**
             * it update nothing
             * if data is untouch or clean
             */
            return response()->json([
                "message" => "Failed to update",
                "result"  => false
                ]);
        }
    }

    /**
     * method to update password
     * first check if the inputed password is same as prevoius
     * then there is nothing to update the password
     * else then update the password
     * 
     * @param Request $request $id
     * 
     * @return response
     */
    public function updatePassword(Request $request, $id){

        //get the user info first
        $Users  = User::where('iduser', $id)
                        ->where('delete', 0)
                        ->get();
        
        $inputPass = $request->password;

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;
        
        if($cursor->count() > 0 ) {
            
            foreach ($cursor as $User) {
                $oldPass = $User->password;
            }   
                //instantiate PasswordEncrypt
                $hash = new UtilityController();
                
                /**
                 * check if password is same with inputed password
                 * if the same then tell there is nothing to update
                 * else then hash the inputed password string and update the table
                 * 
                 */
                if($hash->verifyPassword($inputPass, $oldPass)) {
                    return response()->json([
                        "message" => "there is nothing to update.",
                        "result"  => false
                    ]);
                } else {
                    //update password
                    $updateUser = User::where('iduser', $id);
                    $updateUser->update(['password' => $hash->hash($inputPass)]);
                    return response()->json([
                        "message" => "User password Updated.",
                        "result"  => true
                    ]);             
                } 
        } else {
            return response()->json([
                "message" => "User not found.",
                "result"  => false
            ]);
        }
    }

    
    /**
     * method to soft delete user
     * 
     * @param $id
     * 
     * @return response
     */
    public function deleteUser($id){

        $Users  = User::all()
                        ->where('iduser', $id)
                        ->where('delete', 0);        

        if($Users->count() > 0 ) {          
            //soft delete user
            $deleteUser = User::where('iduser', $id);
            if($deleteUser->update(['delete' => '1'])){
                return response()->json([
                    "message" => "Account is Deleted.",
                    "result"  => true
                ]);
            } else {
                return response()->json([
                    "message" => "Account Deletion Failed.",
                    "result"  => false
                ]);
                return response()->json([
                    "message" => "Account Deletion Failed.",
                    "result"  => false
                ]);
            }              
        } else {
            return response()->json([
                "message" => "User not found.",
                "result"  => false
            ]);    
        }
    }

    /**
     * method to save password for new Client registered via SNS
     * 
     * @param Request $request, $id id of client
     * 
     * @return response
     */
    public function setPassword(Request $request, $id){

        //get the user info first
        $Users  = User::where('iduser', $id)
                        ->where('delete', 0)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;
        
        if($cursor->count() > 0 ) {

            //instantiate PasswordEncrypt
            $hash = new UtilityController();           
            
            //update password
            $updateUser = User::where('iduser', $id);
            
            if($updateUser->update([
                'password' => $hash->hash($request->password)
                ])) {
                return response()->json([
                    "message" => "User password is set.",
                    "result"  => true
                ]);
            }else {
                return response()->json([
                    "message" => "failed to set password.",
                    "result"  => false
                ]);
            }            
        } else {
            return response()->json([
                "message" => "User not found.",
                "result"  => false
            ]);
        }
    }

    /**
     * method to search for duplicate email
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function findDuplicateEmail(Request $request){

        $check = '';

        //check if email already registered
        $Users  = User::where('email', $request->email)->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;

        if($cursor->count() > 0 ){
            return response()->json([
                "message" => "email exist.",
                "result"  => false
            ]);
        }else{
            return response()->json([
                "message" => "email does not exist.",
                "result"  => true
            ]);
        }
    }

     /**
     * method to search for duplicate email
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function findDuplicateNickname(Request $request){

        $check = '';
        //check if email already registered
        $Users  = User::where('nickname', $request->nickname)->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;

        if($cursor->count() > 0 ){
            return response()->json([
                "message" => "nickname exist.",
                "result"  => false
            ]);
        }else{
            return response()->json([
                "message" => "nickname does not exist.",
                "result"  => true
            ]);
        }
    }
}
