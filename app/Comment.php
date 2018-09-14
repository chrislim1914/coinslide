<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcomment', 'idcontent', 'iduser', 'content', 'createdate', 'modifieddate', 'delete'
    ];    
}
