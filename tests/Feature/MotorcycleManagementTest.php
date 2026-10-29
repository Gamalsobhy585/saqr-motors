<?php

namespace Tests\Feature;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Enums\UserRole;
use App\Filament\Resources\Motorcycles\MotorcycleResource;
use App\Filament\Resources\Motorcycles\Pages\CreateMotorcycle;
use App\Filament\Resources\Motorcycles\Pages\EditMotorcycle;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MotorcycleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel('admin');
    }

    private function makeUser(UserRole $role, bool $active = true): User
    {
        $user = User::factory()->make();
        $user->role = $role;
        $user->is_active = $active;
        $user->save();

        return $user;
    }

    private function makeBrand(string $slug): Brand
    {
        return Brand::query()->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function makeMotorcycle(Brand $brand, string $slug = 'test-bike'): Motorcycle
    {
        return Motorcycle::factory()
            ->for($brand)
            ->create(['name' => 'Test Bike', 'slug' => $slug]);
    }

    private function formData(Brand $brand, string $slug = 'new-bike'): array
    {
        return [
            'brand_id' => $brand->id,
            'name' => 'New Bike',
            'slug' => $slug,
            'category' => MotorcycleCategory::Urban->value,
            'model_year' => 2025,
            'starting_price' => null,
            'currency_code' => 'EGP',
            'availability_status' => MotorcycleAvailability::Available->value,
            'is_featured' => false,
            'is_published' => false,
            'sort_order' => 0,
        ];
    }

    public function test_two_admins_can_manage_any_brand(): void
    {
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $bike = $this->makeMotorcycle($yamaha);

        foreach ([$this->makeUser(UserRole::Admin), $this->makeUser(UserRole::Admin)] as $admin) {
            $this->assertTrue($admin->can('create', Motorcycle::class));
            $this->assertTrue($admin->can('update', $bike));
            $this->assertTrue($admin->can('delete', $bike));
        }

        $this->assertNotEquals($honda->id, $yamaha->id);
    }

    public function test_data_entry_can_access_multiple_assigned_brands_only(): void
    {
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $ducati = $this->makeBrand('ducati');
        $dataEntry = $this->makeUser(UserRole::DataEntry);
        $dataEntry->brands()->sync([$honda->id, $yamaha->id]);

        $hondaBike = $this->makeMotorcycle($honda, 'cb650r');
        $yamahaBike = $this->makeMotorcycle($yamaha, 'mt-07');
        $ducatiBike = $this->makeMotorcycle($ducati, 'monster');

        $this->assertTrue($dataEntry->can('create', Motorcycle::class));
        $this->assertTrue($dataEntry->can('update', $hondaBike));
        $this->assertTrue($dataEntry->can('update', $yamahaBike));
        $this->assertFalse($dataEntry->can('update', $ducatiBike));
        $this->assertFalse($dataEntry->can('delete', $hondaBike));

        $this->actingAs($dataEntry);

        $this->assertEqualsCanonicalizing(
            [$hondaBike->id, $yamahaBike->id],
            MotorcycleResource::getEloquentQuery()->pluck('motorcycles.id')->all(),
        );
    }

    public function test_unassigned_data_entry_cannot_create_a_motorcycle(): void
    {
        $dataEntry = $this->makeUser(UserRole::DataEntry);

        $this->assertFalse($dataEntry->can('create', Motorcycle::class));
    }

    public function test_inactive_data_entry_cannot_access_motorcycles(): void
    {
        $brand = $this->makeBrand('honda');
        $bike = $this->makeMotorcycle($brand);
        $dataEntry = $this->makeUser(UserRole::DataEntry, false);
        $dataEntry->brands()->attach($brand);

        $this->assertFalse($dataEntry->can('viewAny', Motorcycle::class));
        $this->assertFalse($dataEntry->can('view', $bike));
        $this->assertFalse($dataEntry->can('create', Motorcycle::class));
    }

    public function test_admin_can_create_motorcycle_using_filament_form(): void
    {
        $admin = $this->makeUser(UserRole::Admin);
        $brand = $this->makeBrand('honda');

        $this->actingAs($admin);

        Livewire::test(CreateMotorcycle::class)
            ->fillForm($this->formData($brand))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('motorcycles', [
            'brand_id' => $brand->id,
            'slug' => 'new-bike',
            'created_by' => $admin->id,
        ]);
    }

    public function test_unassigned_brand_cannot_be_submitted_when_creating(): void
    {
        $assigned = $this->makeBrand('honda');
        $unassigned = $this->makeBrand('ducati');
        $dataEntry = $this->makeUser(UserRole::DataEntry);
        $dataEntry->brands()->attach($assigned);
        $this->actingAs($dataEntry);

        Livewire::test(CreateMotorcycle::class)
            ->fillForm($this->formData($unassigned))
            ->call('create')
            ->assertForbidden();

        $this->assertDatabaseMissing('motorcycles', [
            'brand_id' => $unassigned->id,
            'slug' => 'new-bike',
        ]);
    }

    public function test_data_entry_cannot_move_existing_motorcycle_to_unassigned_brand(): void
    {
        $assigned = $this->makeBrand('honda');
        $unassigned = $this->makeBrand('ducati');
        $dataEntry = $this->makeUser(UserRole::DataEntry);
        $dataEntry->brands()->attach($assigned);
        $bike = $this->makeMotorcycle($assigned);
        $this->actingAs($dataEntry);

        Livewire::test(EditMotorcycle::class, ['record' => $bike->id])
            ->fillForm(['brand_id' => $unassigned->id])
            ->call('save')
            ->assertForbidden();

        $this->assertSame($assigned->id, $bike->fresh()->brand_id);
    }

    public function test_slug_must_be_unique_per_brand_but_can_repeat_across_brands(): void
    {
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $this->makeMotorcycle($honda, 'shared-slug');
        $admin = $this->makeUser(UserRole::Admin);
        $this->actingAs($admin);

        Livewire::test(CreateMotorcycle::class)
            ->fillForm($this->formData($honda, 'shared-slug'))
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);

        Livewire::test(CreateMotorcycle::class)
            ->fillForm($this->formData($yamaha, 'shared-slug'))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('motorcycles', [
            'brand_id' => $yamaha->id,
            'slug' => 'shared-slug',
        ]);
    }

    public function test_assigned_data_entry_can_create_motorcycle_through_filament(): void
    {
        $brand = $this->makeBrand('honda');
        $dataEntry = $this->makeUser(UserRole::DataEntry);
        $dataEntry->brands()->attach($brand);
        $this->actingAs($dataEntry);

        Livewire::test(CreateMotorcycle::class)
            ->fillForm($this->formData($brand, 'cb500f'))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('motorcycles', [
            'brand_id' => $brand->id,
            'slug' => 'cb500f',
            'created_by' => $dataEntry->id,
        ]);
    }

    public function test_specification_is_saved_under_its_motorcycle(): void
    {
        $brand = $this->makeBrand('yamaha');
        $motorcycle = $this->makeMotorcycle($brand);

        $motorcycle->specification()->create([
            'engine_cc' => 689,
            'engine_type' => 'Parallel twin',
            'additional_specs' => ['cooling' => 'Liquid cooled'],
        ]);

        $this->assertSame(689, $motorcycle->specification->engine_cc);
        $this->assertSame(
            'Liquid cooled',
            $motorcycle->specification->additional_specs['cooling'],
        );
    }

    public function test_first_gallery_image_becomes_the_only_primary_image(): void
    {
        $brand = $this->makeBrand('honda');
        $bike = $this->makeMotorcycle($brand);
        $bike->images()->createMany([
            ['path' => 'motorcycles/images/second.jpg', 'sort_order' => 2, 'is_primary' => false],
            ['path' => 'motorcycles/images/first.jpg', 'sort_order' => 1, 'is_primary' => false],
        ]);

        $bike->syncPrimaryImage();

        $this->assertSame(1, $bike->images()->where('is_primary', true)->count());
        $this->assertSame(
            'motorcycles/images/first.jpg',
            $bike->primaryImage()->firstOrFail()->path,
        );
    }
}
