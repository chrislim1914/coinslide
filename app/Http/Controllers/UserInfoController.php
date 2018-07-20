<?php

namespace App\Http\Controllers;

use App\UserInfo;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


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

    public function try($id){        
        
        //we convert the $id to integer
        $iduser = intval($id);

        $userinfo = DB::connection('mongodb')->collection('userinformations')
                        ->where('iduser', '=', $iduser)
                        ->get();

        $user = DB::table('users')
                //->join($userinfo, 'users.iduser', '=', 'iduser')
                ->select('users.iduser','users.nickname')
                ->where('users.iduser', $id)
                ->get();
        $collection = $userinfo->merge($user);
        return response()->json($collection);
    }
}
