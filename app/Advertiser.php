<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idadvertiser', 'iduser', 'company_name', 'business_registration', 'business_category', 'representative_name', 
        'representative_contactno', 'company_website', 'email', 'password', 'delete'
    ];
}
