<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employ extends Model
{
    protected $table = 'employ';
    protected $primaryKey = 'employ_id';
    public $timestamps = false;
    public function role()
    {
        // Parameter kedua adalah foreign key di tabel employ
        return $this->belongsTo(Role::class, 'role_id'); 
    }
}