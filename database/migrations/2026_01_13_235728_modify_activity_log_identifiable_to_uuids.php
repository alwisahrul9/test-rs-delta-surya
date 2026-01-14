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
        Schema::table('activity_log', function (Blueprint $table) {
            // Mengubah causer_id dan subject_id menjadi string/uuid
            $table->string('subject_id')->nullable()->change();
            $table->string('causer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Mengubah causer_id dan subject_id menjadi string/uuid
            $table->dropColumn('subject_id');
            $table->dropColumn('causer_id');
        });
    }
};
