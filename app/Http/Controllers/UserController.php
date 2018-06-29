<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class UserController extends Controller
{

    /** 
    * Display a listing of the resource. 
    * 
    * @return \Illuminate\Http\Response 
    */ 
   public function authenticate(Request $request){
 
        $this->validate($request, [ 
            'email' => 'required', 
            'password' => 'required' 
            ]);

        $user = User::where('email', $request->input('email'))->first();
        echo $request->input('password');
        echo $user->password;

        $hash = new PasswordEncrypt();

        if($hash->verifyPassword($request->input('password'), $user->password)){  

            $apikey = base64_encode(str_random(40));
            return response()->json([
                                'status' => 'success',
                                'api_key' => $apikey
                                ]);    
        }else{    
            return response()->json(['status' => 'fail'],401);    
        } 
   }
    /**
     * method to retrieved all active users
     * 
     * @return Response
     */
    public function readUsers(){
                
        $Users  = User::where('delete', 0)
                        ->orderBy('last_name', 'asc')
                        ->paginate(5);
        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;

        if($cursor->count() > 0 ) {
            return response()->json($Users);
        } else {
            echo json_encode(
                array("message" => "No Users are found.")
            );
        }
    }

    /**
     * method to retrieved single user by iduser
     * 
     * @param $id
     * @return Response
     */    
    public function getUser($id){

        $Users  = User::all()
                        ->where('iduser', $id)
                        ->where('delete', 0);
        
        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;
        
        if($cursor->count() > 0 ) {
            foreach ($cursor as $User) {
                return response()->json([   
                        'iduser'            => $id, 
                        'first_name'        => $User->first_name,
                        'last_name'         => $User->last_name,
                        'email'             => $User->email,
                        'phone'             => $User->phone,
                        'nickname'          => $User->nickname,
                        'createdate'        => $User->createdate,
                        'national'          => $User->national,
                        'snsProviderName'   => $User->snsProviderName,
                        'snsProviderId'     => $User->snsProviderId
                ]);
            }            
        } else {
            echo json_encode(
                array("message" => "User not found.")
            );
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

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $Users;        

        if($cursor->count() > 0 ) {

            echo json_encode(
                array("message" => "email already registered.")
            );

        } else {

            $hash = new PasswordEncrypt();        
            $User = new User();
            $User->first_name = $request->first_name;
            $User->last_name = $request->last_name;
            $User->email = $request->email;
            $User->phone = $request->phone;
            $User->nickname = $request->nickname;
            $User->password = $hash->hash($request->password);//password hash
            $User->national = $request->national;
            
            if($User->save()) {
                echo json_encode(
                    array("message" => "New User Created.")
                );
            } else {
                echo json_encode(
                    array("message" => "User not created.")
                );
            }
        }       
    }

    /**
     * method update user data excluding password
     * 
     * @param  Request  $request $id
     */
    public function updateUser(Request $request, $id){

        /*
         * find the user
         */
        $Users  = User::all()
                        ->where('iduser', $id)
                        ->where('delete', 0);        

        if($Users->count() > 0 ) {
            /**
             * check again the email if already registered
             */
            $User = User::where('email', $request->email)
                        ->where('iduser', '<>', $id)
                        ->get();
            if($User->count() > 0) {
                echo json_encode(
                    array("message" => "Email already registered!")
                ); 
            } else {

                $updateUser = User::where('iduser', $id);
                if($updateUser->update([
                                'first_name' => $request->first_name,
                                'last_name' => $request->last_name,
                                'email'     => $request->email,
                                'phone'     => $request->phone,
                                'nickname'  => $request->nickname,
                                'national'  => $request->national
                                        ])) {
                    echo json_encode(
                        array("message" => "User Info Updated.")
                    );
                } else {
                    echo json_encode(
                        array("message" => "there is nothing to update.")
                    );
                }
            }

        } else {
            echo json_encode(
                array("message" => "User not found.")
            );           
        }
    }
    
    /**
     * method to search user table by first name, last name, nickname, email
     * with pagination
     * 
     * @return Request $request responce 
     */
    public function searchUser(Request $request){

        /**
         * check if search string is null before we query
         */
        if($request->search == null) {
            echo json_encode(
                array("message" => "No Search String.")
            );
        } else {
            $Users = User::where('first_name', 'LIKE', "%$request->search%")
                              ->orWhere('last_name', 'LIKE', "%$request->search%")
                              ->orWhere('nickname', 'LIKE', "%$request->search%")
                              ->orWhere('email', 'LIKE', "%$request->search%")
                              ->paginate(5);

            //the cursor method may be used to greatly reduce your memory usage:
            $cursor = $Users;

            if($cursor->count() > 0 ) {
                return response()->json($cursor);
            } else {
                echo json_encode(
                    array("message" => "No User are found.")
                );
            }
        }        
    }
}
