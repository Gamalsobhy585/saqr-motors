<?php

namespace Tests\Feature;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Brand;
use App\Models\Motorcycle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaqrPublicNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_price_presets_use_strict_less_than_and_combine_with_brand(): void
    {
        $honda = Brand::query()->create([
            'name' => 'Honda', 'slug' => 'honda', 'is_active' => true, 'sort_order' => 1,
        ]);

        foreach ([['Budget Bike', 'budget-bike', 99999], ['Limit Bike', 'limit-bike', 100000]] as [$name, $slug, $price]) {
            Motorcycle::query()->create([
                'brand_id' => $honda->id,
                'name' => $name,
                'slug' => $slug,
                'category' => MotorcycleCategory::Urban,
                'model_year' => 2026,
                'starting_price' => $price,
                'currency_code' => 'EGP',
                'availability_status' => MotorcycleAvailability::Available,
                'is_published' => true,
            ]);
        }

        $this->get(route('motorcycles.index', ['brand' => 'honda', 'price_under' => 100000]))
            ->assertOk()
            ->assertSee('Budget Bike')
            ->assertDontSee('Limit Bike');
    }

    public function test_guest_session_locale_is_used_on_public_homepage(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('اكتشف الماركات');
    }

    public function test_unpublished_motorcycles_do_not_inflate_public_brand_counts(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Honda', 'slug' => 'honda', 'is_active' => true, 'sort_order' => 1,
        ]);
        Motorcycle::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Not Published', 'slug' => 'not-published',
            'category' => MotorcycleCategory::Urban,
            'availability_status' => MotorcycleAvailability::ComingSoon,
            'is_published' => false,
            'currency_code' => 'EGP',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('0 motorcycles')
            ->assertDontSee('Not Published');
    }
}
