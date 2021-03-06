<?php
/**
 * author: Christopher M. Lim
 * email: lm.chrstphr.m@gmail.com
 * 2018
 */

namespace App\Http\Controllers;

use App\Http\Controllers\AdsSubscription;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class UtilityController extends Controller
{
    public $hashPassword;
    private $image;
    public $currentDateTime;

    //Password Encryption Function

    /**
     * method to hash password using bcrypt
     * note that bcrypt is design to encrypt but not to retrieved
     * the hashed password
     * 
     * @param $password
     * 
     * @return $hashPassword
     */
    public function hash($password) {
        $options = array(
            'cost' => 12,
          );
        $this->hashPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        return trim($this->hashPassword);
    }

    /**
     * 
     * method to verify password using native php password_verify
     * 
     * @param $password $hashedPassword
     * 
     * @return Bool
     */
    public function verifyPassword($password, $hashedPassword) {
        if(password_verify($password, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Return the path to public dir
    * @param null $path
    * @return string
    */
    public function public_path($path=null){
            return rtrim(app()->basePath('public/'.$path), '/');
    }

    // Image resize Function

    /**
     * method for image resize
     * 
     * @param $newImage
     *  
     * @return $this->image
     */
    public function bannerResize($newImage){

        $image = Image::make($newImage);

        $imagewidth = $image->width();
        $imageheight = $image->height();
        
        if($imagewidth == 1200 && $imageheight == 380) {
            return $this->image = $image;
        } else {
            return $this->image = Image::make($newImage)->resize(1200, 380);
        }
    }

    public function advertiserBannerResize($newImage){

        $image = Image::make($newImage);

        $imagewidth = $image->width();
        $imageheight = $image->height();
        
        if($imagewidth == 1100 && $imageheight == 450) {
            return $this->image = $image;
        } else {
            return $this->image = Image::make($newImage)->resize(1100, 450);
        }
    }

    public function profilephotoResize($newImage){

        $image = Image::make($newImage);

        $imagewidth = $image->width();
        $imageheight = $image->height();
        
        if($imagewidth == 300 && $imageheight == 300) {
            return $this->image = $image;
        } else {
            return $this->image = Image::make($newImage)->resize(300, 300);
        }
    }

    public function contentResize($newImage){

        $image = Image::make($newImage);

        $imagewidth = $image->width();
        $imageheight = $image->height();               
        
        return $this->image = Image::make($newImage)->resize($imagewidth,  $imageheight);
    }

    // DateTime Function

    /**
     * set date and time with timezone
     * 
     * as of now the default time zone will be
     * manila, philippines
     */
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
        } elseif($timelapse->diffInHours($current) <= 24) {
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

    /**
     * method to determine hot content
     * $points = number of comment + (likes - dislike)
     * $timelapse = createdate where >= 72 hours && < 360 hours
     * @param $timelapse $points
     * 
     * @return Bool
     */
    public function isItHot($timelapse, $points){

        $timelapse = Carbon::parse($timelapse);        

        $current = $this->setDatetime();

        if($timelapse->diffInHours($current) >= 72 && $timelapse->diffInHours($current) < 360 && $points <> 0) {
            $left = $timelapse->diffInHours($current);
            return true;
        } else {
            return false;
        }
    }

    /**
     * method to determine trending content
     * $points = number of comment + (likes - dislike)
     * $timelapse = createdate where < 360 hours
     * @param $timelapse $points
     * 
     * @return Bool
     */
    public function isItTrending($timelapse, $points){

        $timelapse = Carbon::parse($timelapse);        

        $current = $this->setDatetime();

        if($timelapse->diffInHours($current) >= 360 && $points <> 0) {
            $left = $timelapse->diffInHours($current);
            return true;
        } else {
            return false;
        }
    }

    /**
     * method to iterate date range
     * 
     * @param $range
     * ex. 1week, 1month, etc
     * 
     * @return $periodArray
     */
    public function dateRange($range){
        if($range == null){
            return response()->json([
                'message'   =>  'no search period'
            ]);
        }

        $dateParam = ltrim($range, 1);

        $endDate = Carbon::now();
        $end = $endDate->toDateString();

        if($dateParam === 'week'){
            $startDate = $endDate->subWeek();
            $start = $startDate->toDateString();
        }else{
            $startDate = $endDate->subMonth($range[0]);
            $start = $startDate->toDateString();
        }
       
        $period = CarbonPeriod::create($start, '1 days', $end);

        foreach ($period as $key => $date) {
            
            $date->format('Y-m-d');

            $rediodArray[] = $date->toDateString();
        }
        
        return $rediodArray;
    }
}
