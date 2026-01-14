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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('examination_id')->constrained('examinations')->onDelete('cascade');
            $table->foreignUuid('pharmacist_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('total_price');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
