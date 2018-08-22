<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idreply', 'idcomment', 'iduser', 'content', 'createdate', 'modifieddate', 'delete'
    ];
}
