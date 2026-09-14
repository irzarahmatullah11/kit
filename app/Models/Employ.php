<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employ extends Authenticatable
{
    protected $table = 'employ';

    protected $primaryKey = 'employ_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['employ_id', 'employ_name', 'email', 'password', 'role'];

    public function roleData()
    {
        return $this->belongsTo(Role::class, 'role', 'role_id');
    }
}
