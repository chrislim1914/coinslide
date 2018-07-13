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
            return $timelapse->diffInMinutes($current) . ' minutes ago';
        } elseif($timelapse->diffInHours($current) <= 12) {
            return $timelapse->diffInHours($current). ' hours ago';
        } elseif($timelapse->diffInDays($current) <= 6) {
            return $timelapse->diffInDays($current). ' days ago';
        } elseif($timelapse->diffInWeeks($current) <= 4){
            return $timelapse->diffInWeeks($current). ' weeks ago';
        } elseif($timelapse->diffInMonths($current) <= 12){
            return $timelapse->diffInMonths($current). ' Months ago';
        } else {
            return $timelapse->diffInYears($current). ' years ago';
        }
    }
}
