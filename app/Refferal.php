<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refferal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idrefferal', 'recommended_by_nickname', 'recommended_to_nickname', 
    ];
}
