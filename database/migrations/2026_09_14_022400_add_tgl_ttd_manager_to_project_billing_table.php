<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('project_billing', 'tgl_ttd_manager')) {
            Schema::table('project_billing', function (Blueprint $table) {
                $table->date('tgl_ttd_manager')->nullable()->after('tgl_paraf_pm');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The column is also part of the original table migration, so it cannot be safely removed here.
    }
};
