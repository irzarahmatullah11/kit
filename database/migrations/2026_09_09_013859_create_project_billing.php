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
        Schema::create('project_billing', function (Blueprint $table) {
            $table->increments('billing_id');
            $table->unsignedInteger('project_id');
            $table->string('kategori_layanan');
            $table->string('tipe_pengadaan')->nullable();
            $table->string('priode');
            $table->decimal('nilai_bulan', 15, 2)->nullable();
            $table->date('due_date_kontrak')->nullable();
            $table->date('tgl_paraf_pm')->nullable();
            $table->date('tgl_ttd_manager')->nullable();
            $table->date('tgl_pembuatan_ba')->nullable();
            $table->date('tgl_submit_dokumen')->nullable();
            $table->date('tgl_permintaan_invoice')->nullable();
            $table->string('status')->nullable();
            $table->text('note')->nullable();
            $table->string('file_kontrak')->nullable();
            $table->string('file_ba')->nullable();

            // relasi
            $table->foreign('project_id')->references('project_id')->on('project')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_billing');
    }
};
