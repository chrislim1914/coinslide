<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagAdvertiser extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idtag', 'advertiser_tag_name', 
    ];

}
