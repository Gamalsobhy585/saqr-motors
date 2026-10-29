<?php

namespace Database\Factories;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Motorcycle> */
class MotorcycleFactory extends Factory
{
    protected $model = Motorcycle::class;

    public function definition(): array
    {
        return [
            // Supply ->for($brand) in tests; the brand FK is required.
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'category' => MotorcycleCategory::Urban->value,
            'description' => fake()->sentence(),
            'model_year' => 2025,
            'starting_price' => null,
            'currency_code' => 'EGP',
            'availability_status' => MotorcycleAvailability::Available->value,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
