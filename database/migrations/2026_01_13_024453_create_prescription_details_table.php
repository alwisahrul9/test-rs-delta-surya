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
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->string('medicine_id');
            $table->string('medicine_name');
            $table->integer('qty');
            $table->integer('unit_price');
            $table->integer('sub_total'); // qty * unit_price
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
    }
};
