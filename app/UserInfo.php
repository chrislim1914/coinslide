<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserInfo extends Eloquent
{   

    public $timestamps = false;

    protected $connection = 'mongodb';

    protected $collection = "userinformations";
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id', 'iduser', 'gender', 'profilephoto', 'birth', 'country', 'city', 'mStatus',
    ];    
}
