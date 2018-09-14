<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagAds extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idtag', 'ads_tag_name', 
    ];

}
