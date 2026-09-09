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
        Schema::create('project', function (Blueprint $table) {
            $table->increments('project_id');
            $table->string('project_name');
            $table->string('user');
            $table->string('cost_center');
            $table->string('no_kontrak');
            $table->decimal('nilai_kontrak', 15, 2);
            $table->date('tgl_kontrak');
            $table->unsignedInteger('pm_id');
            $table->unsignedInteger('pmo_id')->nullable();
            $table->timestamps();

            // relasi
            $table->foreign('pm_id')->references('employ_id')->on('employ')->cascadeOnDelete();
            $table->foreign('pmo_id')->references('employ_id')->on('employ')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
