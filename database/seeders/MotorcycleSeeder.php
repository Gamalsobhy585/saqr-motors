<?php

namespace Database\Seeders;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Brand;
use App\Models\Motorcycle;
use Illuminate\Database\Seeder;

class MotorcycleSeeder extends Seeder
{
    public function run(): void
    {
        // Real model names; pricing intentionally left null: dealership prices
        // and stock status must be maintained by SAQR, not invented by a seeder.
        $items = [
            ['honda', 'CB650R', 'cb650r', MotorcycleCategory::Urban, 649],
            ['yamaha', 'MT-07', 'mt-07', MotorcycleCategory::Urban, 689],
            ['kawasaki', 'Ninja 650', 'ninja-650', MotorcycleCategory::Performance, 649],
            ['suzuki', 'V-Strom 650', 'v-strom-650', MotorcycleCategory::Adventure, 645],
            ['bmw-motorrad', 'R 1300 GS', 'r-1300-gs', MotorcycleCategory::Premium, 1300],
            ['ducati', 'Monster', 'monster', MotorcycleCategory::Performance, 937],
            ['ktm', '390 Adventure', '390-adventure', MotorcycleCategory::Adventure, null],
            ['triumph', 'Tiger 900', 'tiger-900', MotorcycleCategory::Adventure, 888],
        ];

        foreach ($items as $index => [$brandSlug, $name, $slug, $category, $engineCc]) {
            $brand = Brand::query()->where('slug', $brandSlug)->first();

            if (! $brand) {
                throw new \RuntimeException("Missing brand '{$brandSlug}'. Run BrandSeeder first.");
            }

            $motorcycle = Motorcycle::query()->firstOrCreate(
                ['brand_id' => $brand->id, 'slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => "{$brand->name} {$name} — sample catalog listing. Verify details before publication.",
                    'model_year' => 2025,
                    'starting_price' => null,
                    'currency_code' => 'EGP',
                    'availability_status' => MotorcycleAvailability::ComingSoon,
                    'is_featured' => false,
                    'is_published' => false,
                    'sort_order' => $index + 1,
                ]
            );

            if ($engineCc !== null) {
                $motorcycle->specification()->firstOrCreate([], [
                    'engine_cc' => $engineCc,
                ]);
            }
        }
    }
}
