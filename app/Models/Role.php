<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';

    public function employees()
    {
        return $this->hasMany(Employ::class, 'role_id');
    }
}