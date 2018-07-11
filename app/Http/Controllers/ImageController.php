<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    private $image;

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
        
        if($imagewidth == 1200 && $imageheight == 380) {
            return $this->image = $image;
        } else {
            return $this->image = Image::make($newImage)->resize(1100, 448);
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
}
