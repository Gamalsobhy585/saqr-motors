<?php

namespace Tests\Feature;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Brand;
use App\Models\City;
use App\Models\Enquiry;
use App\Models\Location;
use App\Models\Motorcycle;
use App\Models\TestRideBooking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaqrPublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createMotorcycle(): Motorcycle
    {
        $brand = Brand::query()->create([
            'name' => 'Honda', 'slug' => 'honda', 'is_active' => true, 'sort_order' => 1,
        ]);

        return Motorcycle::query()->create([
            'brand_id' => $brand->id,
            'name' => 'CB650R', 'slug' => 'cb650r',
            'category' => MotorcycleCategory::Urban,
            'model_year' => 2026,
            'starting_price' => 200000,
            'currency_code' => 'EGP',
            'availability_status' => MotorcycleAvailability::Available,
            'is_published' => true,
        ]);
    }

    public function test_home_catalog_and_detail_are_public(): void
    {
        $motorcycle = $this->createMotorcycle();

        $this->get(route('home'))->assertOk()->assertSee('CB650R');
        $this->get(route('motorcycles.index', [
            'brand' => 'honda', 'category' => MotorcycleCategory::Urban->value,
            'min_price' => 100000, 'max_price' => 250000,
        ]))->assertOk()->assertSee('CB650R');
        $this->get(route('motorcycles.show', ['brand' => 'honda', 'motorcycle' => 'cb650r']))
            ->assertOk()->assertSee('CB650R');
    }

    public function test_two_guests_cannot_book_the_same_slot(): void
    {
        $motorcycle = $this->createMotorcycle();
        $city = City::query()->create(['slug' => 'cairo', 'name_en' => 'Cairo', 'name_ar' => 'القاهرة']);
        $location = Location::query()->create([
            'city_id' => $city->id,
            'name_en' => 'Citystars', 'name_ar' => 'سيتي ستارز',
            'address_en' => 'Nasr City', 'address_ar' => 'مدينة نصر',
        ]);
        $data = [
            'form_type' => 'ride',
            'ride_name' => 'محمد أحمد', 'ride_email' => 'guest@example.test',
            'ride_phone' => '01012345678', 'ride_location_id' => $location->id,
            'ride_date' => today()->addDays(2)->format('Y-m-d'), 'ride_time' => '11:00',
        ];
        $route = route('motorcycles.book', ['motorcycle' => $motorcycle->uuid]);

        $this->post($route, $data)->assertRedirect();
        $this->post($route, $data)->assertSessionHasErrors('ride_time');
        $this->assertDatabaseCount('test_ride_bookings', 1);
    }

    public function test_contact_creates_a_new_enquiry(): void
    {
        $motorcycle = $this->createMotorcycle();

        $this->post(route('motorcycles.contact', ['motorcycle' => $motorcycle->uuid]), [
            'form_type' => 'contact',
            'contact_name' => 'سارة محمد',
            'contact_email' => 'sara@example.test',
            'contact_phone' => '01012345678',
            'contact_message' => 'I would like to know more about this motorcycle.',
        ])->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'motorcycle_id' => $motorcycle->id,
            'email' => 'sara@example.test',
        ]);
    }

    public function test_unpublished_motorcycles_are_not_public(): void
    {
        $motorcycle = $this->createMotorcycle();
        $motorcycle->update(['is_published' => false]);

        $this->get(route('motorcycles.show', ['brand' => 'honda', 'motorcycle' => 'cb650r']))
            ->assertNotFound();
    }
}
