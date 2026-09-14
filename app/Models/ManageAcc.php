<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageAcc extends Model
{
    protected $table = 'employ';
    protected $primaryKey = 'employ_id';
    protected $filable = [
        'employ_name',
        'email',
        'password'
    ];
    protected $hidden = [
        'password', 
    ];
    public $timestamps = false;
}