<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Honda',
                'slug' => 'honda',
                'tagline' => 'Japanese Engineering & Reliability',
                'description' => 'Japanese motorcycle manufacturer offering commuter, sport, touring, adventure, and off-road motorcycles.',
                'website_url' => 'https://global.honda/en/',
            ],
            [
                'name' => 'Yamaha',
                'slug' => 'yamaha',
                'tagline' => 'Performance & Innovation',
                'description' => 'Japanese motorcycle manufacturer known for sport bikes, naked motorcycles, scooters, touring, and off-road models.',
                'website_url' => 'https://global.yamaha-motor.com/',
            ],
            [
                'name' => 'Kawasaki',
                'slug' => 'kawasaki',
                'tagline' => 'High-Performance Motorcycles',
                'description' => 'Japanese motorcycle manufacturer producing Ninja sport bikes, Z naked bikes, adventure motorcycles, and cruisers.',
                'website_url' => 'https://www.kawasaki.com/',
            ],
            [
                'name' => 'Suzuki',
                'slug' => 'suzuki',
                'tagline' => 'Engineering for Every Ride',
                'description' => 'Japanese motorcycle manufacturer offering sport, street, touring, adventure, and off-road motorcycles.',
                'website_url' => 'https://www.globalsuzuki.com/',
            ],
          
            [
                'name' => 'Ducati',
                'slug' => 'ducati',
                'tagline' => 'Italian Performance & Design',
                'description' => 'Italian motorcycle manufacturer known for performance-focused sport bikes, naked motorcycles, adventure bikes, and premium design.',
                'website_url' => 'https://www.ducati.com/',
            ],
          
            [
                'name' => 'Triumph',
                'slug' => 'triumph',
                'tagline' => 'For the Ride',
                'description' => 'British motorcycle manufacturer producing modern classics, roadsters, sport motorcycles, and adventure touring bikes.',
                'website_url' => 'https://www.triumphmotorcycles.com/',
            ],
          
         
            [
                'name' => 'Benelli',
                'slug' => 'benelli',
                'tagline' => 'Italian Motorcycle Heritage',
                'description' => 'Motorcycle brand with Italian origins offering naked, touring, adventure, and sport-oriented motorcycles.',
                'website_url' => 'https://www.benelli.com/',
            ],
         
        ];
        foreach ($brands as $index => $brand) {
            $logoPath = $this->logoPath($brand['slug']);

            $record = Brand::firstOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'tagline' => $brand['tagline'],
                    'description' => $brand['description'],
                    'website_url' => $brand['website_url'],
                    'logo_path' => $logoPath,
                    'cover_path' => null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );

            // Repair missing seeded logo paths without replacing valid uploads.
            if (
                $logoPath &&
                (
                    ! $record->logo_path ||
                    (
                        str_starts_with($record->logo_path, 'images/brands/') &&
                        ! is_file(public_path($record->logo_path))
                    )
                )
            ) {
                $record->update(['logo_path' => $logoPath]);
            }
        }
    }
    private function logoPath(string $slug): ?string
    {
        foreach (['webp', 'png', 'jpg', 'jpeg', 'svg'] as $extension) {
            $path = "images/brands/{$slug}.{$extension}";

            if (is_file(public_path($path))) {
                return $path;
            }
        }

        return null;
    }

}
