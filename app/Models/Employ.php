<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employ extends Authenticatable
{
    protected $table = 'employ'; // Sesuaikan dengan nama tabel hasil migrasi
    protected $primaryKey = 'employ_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['employ_id', 'employ_name', 'email', 'password', 'role'];
}