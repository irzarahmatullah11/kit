<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('project_billing')->update([
            'status' => DB::raw("CASE WHEN tgl_pembuatan_ba IS NOT NULL AND tgl_paraf_pm IS NOT NULL AND tgl_ttd_manager IS NOT NULL AND tgl_submit_dokumen IS NOT NULL AND tgl_permintaan_invoice IS NOT NULL THEN 'Done' ELSE 'In Progress' END"),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
