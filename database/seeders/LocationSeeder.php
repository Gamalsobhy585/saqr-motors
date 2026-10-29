<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Location;
use Illuminate\Database\Seeder;
use RuntimeException;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CitySeeder::class);

        // Actual Egyptian malls; addresses are general descriptions.
        // Phone numbers are FICTIONAL demonstration data, NOT the malls' contacts.
        // Google Maps URLs are search links, not verified business Place IDs.
        $locations = [
            [
                'city' => 'cairo', 'name_ar' => 'سيتي ستارز', 'name_en' => 'Citystars Mall',
                'address_ar' => 'شارع عمر بن الخطاب، مدينة نصر، القاهرة',
                'address_en' => 'Omar Ibn El Khattab Street, Nasr City, Cairo',
                'phone' => '+201000000001',
            ],
            [
                'city' => 'cairo', 'name_ar' => 'كايرو فيستيفال سيتي مول', 'name_en' => 'Cairo Festival City Mall',
                'address_ar' => 'شارع التسعين، التجمع الخامس، القاهرة الجديدة',
                'address_en' => '90th Street area, Fifth Settlement, New Cairo',
                'phone' => '+201000000002',
            ],
            [
                'city' => 'cairo', 'name_ar' => 'سيتي سنتر ألماظة', 'name_en' => 'City Centre Almaza',
                'address_ar' => 'طريق السويس، ألماظة، القاهرة',
                'address_en' => 'Suez Road, Almaza, Cairo',
                'phone' => '+201000000003',
            ],
            [
                'city' => 'giza', 'name_ar' => 'مول مصر', 'name_en' => 'Mall of Egypt',
                'address_ar' => 'طريق الواحات، مدينة السادس من أكتوبر، الجيزة',
                'address_en' => 'Al Wahat Road, 6th of October City, Giza',
                'phone' => '+201000000004',
            ],
            [
                'city' => 'giza', 'name_ar' => 'مول العرب', 'name_en' => 'Mall of Arabia',
                'address_ar' => 'ميدان جهينة، مدينة السادس من أكتوبر، الجيزة',
                'address_en' => 'Juhayna Square, 6th of October City, Giza',
                'phone' => '+201000000005',
            ],
            [
                'city' => 'alexandria', 'name_ar' => 'سيتي سنتر الإسكندرية', 'name_en' => 'City Centre Alexandria',
                'address_ar' => 'طريق القاهرة الإسكندرية الصحراوي، محرم بك، الإسكندرية',
                'address_en' => 'Cairo–Alexandria Desert Road, Moharram Bek, Alexandria',
                'phone' => '+201000000006',
            ],
            [
                'city' => 'alexandria', 'name_ar' => 'سان ستيفانو جراند بلازا', 'name_en' => 'San Stefano Grand Plaza',
                'address_ar' => 'طريق الجيش، سان ستيفانو، الإسكندرية',
                'address_en' => 'El-Gaish Road, San Stefano, Alexandria',
                'phone' => '+201000000007',
            ],
        ];

        foreach ($locations as $data) {
            $city = City::query()->where('slug', $data['city'])->first();
            if (! $city) {
                throw new RuntimeException("City not found: {$data['city']}");
            }

            $mapsQuery = rawurlencode($data['name_en'] . ', ' . $city->name_en . ', Egypt');

            // Match an existing legacy record by old name (when present), to avoid duplicates.
            $location = Location::query()
                ->where('name_en', $data['name_en'])
                ->orWhere('name', $data['name_en'])
                ->orWhere('name', $data['name_ar'])
                ->first();

            if (! $location) {
                Location::query()->create([
                    'city_id' => $city->id,
                    'name_ar' => $data['name_ar'],
                    'name_en' => $data['name_en'],
                    'address_ar' => $data['address_ar'],
                    'address_en' => $data['address_en'],
                    'phone' => $data['phone'],
                    'google_maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery,
                ]);
                continue;
            }

            // Preserve edited phone and map URL on existing records, but fill bilingual data.
            $location->fill([
                'city_id' => $city->id,
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'address_ar' => $data['address_ar'],
                'address_en' => $data['address_en'],
            ]);
            if (blank($location->phone)) $location->phone = $data['phone'];
            if (blank($location->google_maps_url)) {
                $location->google_maps_url = 'https://www.google.com/maps/search/?api=1&query=' . $mapsQuery;
            }
            $location->save();
        }
    }
}
