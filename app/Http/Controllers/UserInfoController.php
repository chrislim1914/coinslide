<?php

namespace App\Http\Controllers;

use App\UserInfo;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class UserinfoController extends Controller {

    public function all(){
        $userinfo = UserInfo::all();

        return response()->json($userinfo);
    }

    public function try(){        
        
        $userinfo = DB::connection('mongodb')->collection('userinformations')
                        ->get();
        print_r($userinfo);
        $user = DB::table('users')
                ->join($userinfo, 'users.iduser', '=', 'iduser')
                ->select('users.iduser','users.nickname')
                ->get();

        return response()->json($user);
    }
}
