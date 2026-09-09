<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\table;

class project_billing extends Model
{
    use HasFactory;

    protected $table = 'project_billing';

    protected $fillable = [
        'project_id',
        'kategori_layanan',
        'tipe_pengadaan',
        'priode',
        'nilai_bulan',
        'due_date_kontrak',
        'tgl_pembuatan_ba',
        'tgl_paraf_pm',
        'tgl_submit_dokumen',
        'tgl_permintaan_invoice',
        'status',
        'note',
        'file_kontrak',
        'file_ba'
    ];
}
