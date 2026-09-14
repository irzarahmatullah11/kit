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
        $managerSignatureDates = [
            1 => '2026-03-01',
            2 => '2026-04-01',
            3 => '2026-08-03',
            4 => '2026-09-03',
            5 => '2026-10-03',
            6 => '2026-11-03',
            7 => '2026-12-03',
            8 => '2026-08-13',
            9 => '2026-09-13',
            10 => '2026-10-13',
            11 => '2026-11-13',
            12 => '2026-12-13',
            13 => '2027-01-13',
            14 => '2027-02-13',
            15 => '2027-03-13',
            16 => '2027-04-13',
            17 => '2027-05-13',
        ];

        foreach ($managerSignatureDates as $projectId => $date) {
            DB::table('project_billing')
                ->where('project_id', $projectId)
                ->whereNull('tgl_ttd_manager')
                ->update(['tgl_ttd_manager' => $date]);
        }

        DB::table('project_billing')->update([
            'status' => DB::raw("CASE WHEN tgl_pembuatan_ba IS NOT NULL AND tgl_paraf_pm IS NOT NULL AND tgl_ttd_manager IS NOT NULL AND tgl_submit_dokumen IS NOT NULL AND tgl_permintaan_invoice IS NOT NULL THEN 'Done' ELSE 'In Progress' END"),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
