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
        Schema::create('employ', function (Blueprint $table) {
            $table->increments('employ_id');
            $table->string('employ_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedInteger('role');

            // relasi
            $table->foreign('role')->references('role_id')->on('role')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employ');
    }
};
