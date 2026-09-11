<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'employ';
    protected $primaryKey = 'employ_id';
    public $timestamps = false;
    protected $filable = [
        'employ_name',
        'email',
        'password'
    ];
    protected $hidden = [
        'password', 
    ];
}