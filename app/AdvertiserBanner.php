<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserBanner extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idadvertiser_banner', 'idadvertiser', 'img', 'position', 'use', 
    ];
}
