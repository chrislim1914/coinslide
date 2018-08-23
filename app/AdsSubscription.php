<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdsSubscription extends Model
{
    public $timestamps = false;
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idsubscription', 'iduser', 'idadvertise', 'startdate', 'enddate', 'use', 
    ];
}
