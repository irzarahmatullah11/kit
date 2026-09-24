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
        Schema::table('project_billing', function (Blueprint $table) {
            $table->string('ms_no')->nullable()->after('project_id');
            $table->text('note_1')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_billing', function (Blueprint $table) {
            $table->dropColumn(['ms_no', 'note_1']);
        });
    }
};
