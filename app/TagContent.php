<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagContent extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idtag', 'contant_tag_name', 
    ];

}
