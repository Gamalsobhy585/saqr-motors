<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycle_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('engine_cc')->nullable();
            $table->string('engine_type', 120)->nullable();
            $table->string('transmission', 120)->nullable();
            $table->decimal('power_hp', 8, 2)->nullable();
            $table->decimal('torque_nm', 8, 2)->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->decimal('fuel_capacity_liters', 6, 2)->nullable();
            $table->unsignedInteger('seat_height_mm')->nullable();
            $table->jsonb('additional_specs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycle_specs');
    }
};
