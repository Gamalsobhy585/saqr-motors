<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_ride_bookings', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
            $table->foreignId('motorcycle_id')->nullable()->constrained('motorcycles')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            $table->string('customer_name');
            $table->string('email');
            $table->string('phone', 25);
            $table->dateTime('scheduled_at');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('scheduled_at');
            $table->index(['brand_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_ride_bookings');
    }
};
