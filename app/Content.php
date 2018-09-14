<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{    
    public $timestamps = false;

    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iduser', 'title', 'content', 'description', 'createdate', 'modifieddate', 'delete'
    ];
}
