<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    protected $primaryKey = 'project_id';

    protected $casts = ['nilai_kontrak' => 'decimal:2', 'tgl_kontrak' => 'date'];

    protected $fillable = ['project_name', 'user', 'cost_center', 'no_kontrak', 'nilai_kontrak', 'tgl_kontrak', 'pm', 'pmo'];

}
