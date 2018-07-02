<?php

namespace App\Http\Controllers;

use App\Likes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LikeController extends Controller
{
    public function all() {
        $Like  = DB::table('likes')
                    ->select(DB::raw('count(islike) as `likes`'))
                    ->where('islike', 1)
                    ->where('idcontent', 1)
                    ->get();
        

        if($Like->count() > 0 ) {
            return response()->json($Like);
        } else {
            echo json_encode(
                array("message" => "No Like are found.")
            );
        }
    }
}
