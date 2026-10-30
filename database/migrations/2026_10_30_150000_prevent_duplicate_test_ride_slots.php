<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicate = DB::table('test_ride_bookings')
            ->whereNotNull('motorcycle_id')->whereNotNull('location_id')
            ->select('motorcycle_id', 'location_id', 'scheduled_at')
            ->groupBy('motorcycle_id', 'location_id', 'scheduled_at')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException('Duplicate test-ride slots exist; resolve them before adding the unique index.');
        }

        Schema::table('test_ride_bookings', function (Blueprint $table): void {
            $table->unique(
                ['motorcycle_id', 'location_id', 'scheduled_at'],
                'test_ride_slot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('test_ride_bookings', function (Blueprint $table): void {
            $table->dropUnique('test_ride_slot_unique');
        });
    }
};
