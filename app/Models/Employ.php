<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employ extends Model
{
    protected $table = 'employ';
    protected $primaryKey = 'employ_id';
    public $timestamps = false;
    public function roleData()
    {
        // belongsTo(Model, foreign_key, owner_key)
        return $this->belongsTo(Role::class, 'role', 'role_id'); 
    }
}