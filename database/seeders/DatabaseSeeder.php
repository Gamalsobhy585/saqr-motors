<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            BrandUserSeeder::class,
            MotorcycleSeeder::class,

            CitySeeder::class,
            LocationSeeder::class,

            FinancialPlanSeeder::class,
            ServiceSeeder::class,
            PromotionSeeder::class,

            EnquirySeeder::class,
            TestRideBookingSeeder::class,
        ]);
    }
}