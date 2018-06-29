<?php

namespace App\Http\Controllers;

use App\Contents;
use App\Http\Controllers\PasswordEncrypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class ContentController extends Controller {

    /**
     * method to read all content in desc order by idcontent
     * 
     * @return Responce
     */
    public function readAllContent(){

        //create query contents inner joint users
        $content = DB::table('contents')
                        ->join('users', 'contents.user_id', '=', 'users.iduser')
                        ->select('contents.idcontent', 'contents.user_id', 'users.nickname', 'contents.title', 'contents.content', 
                                'contents.createdate', 'contents.modifieddate', (DB::raw("'".$this->timeLapse('contents.createdate')."' as timelapse")),
                                'contents.delete')
                        ->where('contents.delete', 0)
                        ->orderBy('idcontent', 'desc')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $content;

        if($cursor->count() > 0 ) {   
                
                return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "No Content are found.")
            );
        }
    }

    /**
     * method to read all content in desc order by idcontent
     * with pagination
     * 
     * @return Responce
     */
    public function contentPaginate(){

        $Contents = Contents::orderBy('idcontent', 'desc')
                        ->paginate(5);
        return response()->json($Contents);
    }

    /**
     * methos for search content by 
     */
    public function searchContent(Request $request){

    }

    public function timeLapse($dt){
        echo $dt.'<br/>';
        $current = Carbon::now();
        echo $newdt = Carbon::parse($dt);
        // Set the timezone via DateTimeZone instance or string
        $current->timezone = new \DateTimeZone('Asia/Manila');

        if($newdt->diffInSeconds($current) <= 59) {
            return $newdt =  'just now';
        } elseif($newdt->diffInMinutes($current) <= 59) {
            return $newdt = $newdt->diffInMinutes($current) . ' minutes ago';
        } elseif($newdt->diffInHours($current) <= 12) {
            return $newdt = $newdt->diffInHours($current). ' hours ago';
        } elseif($newdt->diffInDays($current) <= 6) {
            return $newdt = $newdt->diffInDays($current). ' days ago';
        } elseif($newdt->diffInWeeks($current) <= 4){
            return $newdt = $newdt->diffInWeeks($current). ' weeks ago';
        } elseif($newdt->diffInMonths($current) <= 12){
            return $newdt = $newdt->diffInMonths($current). ' Months ago';
        } else {
            return $newdt = $newdt->diffInYears($current). ' years ago';
        }
    }
}
