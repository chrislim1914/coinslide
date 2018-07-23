<?php

namespace App\Http\Controllers;


use Illuminate\Support\Carbon;

class DateTimeController extends Controller
{
    public $currentDateTime;

    // public function __construct(){

    //     $this->setDateTime();
    // }

    public function setDatetime(){

        //create current time using Carbon
        $current = Carbon::now();

        // Set the timezone via DateTimeZone instance or string
        $current->timezone = new \DateTimeZone(getenv('APP_TIMEZONE'));
        
        return $current;
    }
    
    /**
     * method to compute time lapse against createdate in contents table
     * 
     * @return $timelapse
     */
    public function timeLapse($timelapse){
        
        $timelapse = Carbon::parse($timelapse);        

        $current = $this->setDatetime();

        if($timelapse->diffInSeconds($current) <= 59) {
            return $timelapse =  'just now';
        } elseif($timelapse->diffInMinutes($current) <= 59) {
            $left = $timelapse->diffInMinutes($current);
            return ($left == 1 ? $left. ' minute ago' :  $left. ' minutes ago');
        } elseif($timelapse->diffInHours($current) <= 12) {
            $left = $timelapse->diffInHours($current);
            return ($left == 1 ? $left. ' hour ago' :  $left. ' hours ago');
        } elseif($timelapse->diffInDays($current) <= 6) {
            $left = $timelapse->diffInDays($current);
            return ($left == 1 ? $left. ' day ago' :  $left. ' days ago');
        } elseif($timelapse->diffInWeeks($current) <= 4){
            $left = $timelapse->diffInWeeks($current);
            return ($left == 1 ? $left. ' week ago' :  $left. ' weeks ago');
        } elseif($timelapse->diffInMonths($current) <= 12){
            $left = $timelapse->diffInMonths($current);
            return ($left == 1 ? $left. ' month ago' :  $left. ' months ago');
        } else {
            $left = $timelapse->diffInYears($current);
            return ($left == 1 ? $left. ' year ago' :  $left. ' years ago');
        }
    }

    /**
     * method to compute age
     * 
     * @param $birth
     * 
     * @return $age
     */
    public function findAge($birth){

        /**
         * retrieved the data from mongodb and implode
         * then convert it to Carbon-base date format
         */
        $newbirth = explode("-",$birth);

        $dt = Carbon::now();

        $dt->setDate($newbirth[0], $newbirth[1], $newbirth[2])->toDateString();

        $current = $this->setDatetime();
        
        if($age = $dt->diffInYears($current)) {
            return $age;
        }
    }
}
