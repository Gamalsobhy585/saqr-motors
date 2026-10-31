<?php

namespace Database\Seeders;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Brand;
use App\Models\Motorcycle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MotorcycleSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'brand' => 'honda',
                'name' => 'CB650R',
                'slug' => 'cb650r',
                'category' => MotorcycleCategory::Urban,
                'specs' => [
                    'engine_cc' => 649,
                    'engine_type' => 'Liquid-cooled, inline 4-cylinder, DOHC',
                    'transmission' => '6-speed manual',
                    'power_hp' => 93.9,
                    'torque_nm' => 63,
                    'weight_kg' => 203,
                    'fuel_capacity_liters' => 15.4,
                    'seat_height_mm' => 810,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain', 'ABS' => 'Yes'],
                ],
                'images' => ['cb650r.jpg', 'cb650r-side.jpg'],
            ],
            [
                'brand' => 'yamaha',
                'name' => 'MT-07',
                'slug' => 'mt-07',
                'category' => MotorcycleCategory::Urban,
                'specs' => [
                    'engine_cc' => 689,
                    'engine_type' => 'Liquid-cooled, parallel twin, DOHC',
                    'transmission' => '6-speed manual',
                    'power_hp' => 72.4,
                    'torque_nm' => 68,
                    'weight_kg' => 183,
                    'fuel_capacity_liters' => 14,
                    'seat_height_mm' => 805,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain', 'Engine family' => 'CP2'],
                ],
                'images' => ['mt-07.jpg', 'mt-07-side.jpg'],
            ],
            [
                'brand' => 'kawasaki',
                'name' => 'Ninja 650',
                'slug' => 'ninja-650',
                'category' => MotorcycleCategory::Performance,
                'specs' => [
                    'engine_cc' => 649,
                    'engine_type' => 'Liquid-cooled, parallel twin',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain'],
                ],
                'images' => ['ninja-650.jpg', 'ninja-650-side.jpg'],
            ],
            [
                'brand' => 'suzuki',
                'name' => 'V-Strom 650',
                'slug' => 'v-strom-650',
                'category' => MotorcycleCategory::Adventure,
                'specs' => [
                    'engine_cc' => 645,
                    'engine_type' => 'Liquid-cooled, 90-degree V-twin',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain'],
                ],
                'images' => ['v-strom-650.jpg', 'v-strom-650-side.jpg'],
            ],
            [
                'brand' => 'honda',
                'name' => 'R 1300 GS',
                'slug' => 'r-1300-gs',
                'category' => MotorcycleCategory::Premium,
                'specs' => [
                    'engine_cc' => 1300,
                    'engine_type' => 'Air/liquid-cooled boxer twin',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Engine layout' => 'Boxer twin', 'Final drive' => 'Shaft'],
                ],
                'images' => ['r-1300-gs.jpg', 'r-1300-gs-side.jpg'],
            ],
            [
                'brand' => 'ducati',
                'name' => 'Monster',
                'slug' => 'monster',
                'category' => MotorcycleCategory::Performance,
                'specs' => [
                    'engine_cc' => 937,
                    'engine_type' => 'Liquid-cooled Testastretta L-twin',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Engine family' => 'Testastretta', 'Final drive' => 'Chain'],
                ],
                'images' => ['monster.jpg', 'monster-side.jpg'],
            ],
            [
                'brand' => 'triumph',
                'name' => '390 Adventure',
                'slug' => '390-adventure',
                'category' => MotorcycleCategory::Adventure,
                'specs' => [
                    'engine_cc' => null,
                    'engine_type' => 'Liquid-cooled, single-cylinder',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain'],
                ],
                'images' => ['390-adventure.webp', '390-adventure-side.webp'],
            ],
            [
                'brand' => 'triumph',
                'name' => 'Tiger 900',
                'slug' => 'tiger-900',
                'category' => MotorcycleCategory::Adventure,
                'specs' => [
                    'engine_cc' => 888,
                    'engine_type' => 'Liquid-cooled, inline 3-cylinder',
                    'transmission' => '6-speed manual',
                    'power_hp' => null,
                    'torque_nm' => null,
                    'weight_kg' => null,
                    'fuel_capacity_liters' => null,
                    'seat_height_mm' => null,
                    'additional_specs' => ['Cooling' => 'Liquid', 'Final drive' => 'Chain'],
                ],
                'images' => ['tiger-900.jpg', 'tiger-900-side.jpg'],
            ],
        ];

        // Illustrative assessment values, not verified retail prices.
        $demoCatalog = [
            'cb650r' => ['price' => 420000, 'published' => true, 'featured' => true],
            'mt-07' => ['price' => 390000, 'published' => true, 'featured' => true],
            'ninja-650' => ['price' => 450000, 'published' => true, 'featured' => false],
            'v-strom-650' => ['price' => null, 'published' => false, 'featured' => false],
            'r-1300-gs' => ['price' => 1250000, 'published' => true, 'featured' => true],
            'monster' => ['price' => 720000, 'published' => true, 'featured' => false],
            '390-adventure' => ['price' => null, 'published' => false, 'featured' => false],
            'tiger-900' => ['price' => null, 'published' => false, 'featured' => false],
        ];

        $disk = Storage::disk('public');

        foreach ($items as $index => $item) {
            $demo = $demoCatalog[$item['slug']];
            $brand = Brand::query()->where('slug', $item['brand'])->first();

            if (! $brand) {
                throw new RuntimeException("Missing brand '{$item['brand']}'. Run BrandSeeder first.");
            }

            $motorcycle = Motorcycle::query()->firstOrCreate(
                ['brand_id' => $brand->id, 'slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'description' => "{$brand->name} {$item['name']} — sample catalog listing. Verify specifications before publication.",
                    'model_year' => 2025,
                    'starting_price' => $demo['price'],
                    'currency_code' => 'EGP',
                    'availability_status' => $demo['published']
                        ? MotorcycleAvailability::Available
                        : MotorcycleAvailability::ComingSoon,
                    'is_featured' => $demo['featured'],
                    'is_published' => $demo['published'],
                    'sort_order' => $index + 1,
                ]
            );

            if (blank($motorcycle->uuid)) {
                $motorcycle->uuid = (string) Str::uuid();
                $motorcycle->save();
            }

            // Preserve specifications already edited in Filament.
            $motorcycle->specification()->firstOrCreate([], $item['specs']);

            foreach ($item['images'] as $imageIndex => $filename) {
                $source = public_path("images/motorcycles/{$filename}");

                if (! is_file($source)) {
                    $this->command?->warn("Missing image: {$source}");
                    continue;
                }

                $destination = "motorcycles/images/{$filename}";

                if (! $disk->exists($destination)) {
                    $saved = $disk->put($destination, file_get_contents($source));

                    if (! $saved) {
                        throw new RuntimeException("Could not store image: {$destination}");
                    }
                }

                $motorcycle->images()->firstOrCreate(
                    ['path' => $destination],
                    [
                        'alt_text' => "{$brand->name} {$item['name']}",
                        'sort_order' => $imageIndex + 1,
                    ]
                );
            }

            if (method_exists($motorcycle, 'syncPrimaryImage')) {
                $motorcycle->syncPrimaryImage();
            }
        }
    }
}
