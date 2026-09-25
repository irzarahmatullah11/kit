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
            $table->string('pm');
            $table->string('pmo')->nullable();
            $table->timestamps();
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
