<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employ extends Model
{
    use HasFactory;
    protected $table = 'employ';

    protected $fillable = [
        'employ_name',
        'email',
        'password',
        'role'
    ];
}
