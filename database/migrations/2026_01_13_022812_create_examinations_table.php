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
        Schema::create('examinations', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->timestamp('examination_time');
            $table->integer('height');
            $table->integer('weight');
            $table->string('systole');
            $table->string('diastole');
            $table->string('heart_rate');
            $table->string('respiration_rate');
            $table->string('temperature');
            $table->text('clinical_notes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
