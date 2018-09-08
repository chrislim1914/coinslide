<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserActivity extends Eloquent
{
    public $timestamps = false;

    protected $connection = 'mongodb';

    protected $collection = "useractivities";
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id', 'iduser', 'date', 'idads', 'idsubscription', 'activity', 
    ];    
}
