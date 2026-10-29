<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['slug' => 'cairo', 'name_ar' => 'القاهرة', 'name_en' => 'Cairo'],
            ['slug' => 'giza', 'name_ar' => 'الجيزة', 'name_en' => 'Giza'],
            ['slug' => 'alexandria', 'name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria'],
        ] as $data) {
            City::query()->updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
