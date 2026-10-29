<?php

namespace Tests\Feature;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Enums\UserRole;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Enquiries\Pages\EditEnquiry;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Models\Brand;
use App\Models\Enquiry;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EnquiryManagementTest extends TestCase
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

    private function makeEnquiry(?Brand $brand, string $email): Enquiry
    {
        return Enquiry::query()->create([
            'brand_id' => $brand?->id,
            'motorcycle_id' => null,
            'name' => 'أحمد محمد',
            'email' => $email,
            'phone' => '01012345671',
            'type' => EnquiryType::General,
            'message' => 'أريد الاستفسار عن الأسعار.',
            'status' => EnquiryStatus::New,
        ]);
    }

    public function test_enquiries_cannot_be_created_from_filament(): void
    {
        $admin = $this->makeUser(UserRole::Admin);
        $entry = $this->makeUser(UserRole::DataEntry);

        $this->assertFalse($admin->can('create', Enquiry::class));
        $this->assertFalse($entry->can('create', Enquiry::class));
        $this->assertArrayNotHasKey('create', EnquiryResource::getPages());
    }

    public function test_admin_can_view_update_and_delete_any_brand_enquiry(): void
    {
        $admin = $this->makeUser(UserRole::Admin);
        $hondaEnquiry = $this->makeEnquiry($this->makeBrand('honda'), 'honda@example.com');
        $yamahaEnquiry = $this->makeEnquiry($this->makeBrand('yamaha'), 'yamaha@example.com');

        foreach ([$hondaEnquiry, $yamahaEnquiry] as $enquiry) {
            $this->assertTrue($admin->can('view', $enquiry));
            $this->assertTrue($admin->can('update', $enquiry));
            $this->assertTrue($admin->can('delete', $enquiry));
        }
    }

    public function test_data_entry_only_manages_assigned_brands_and_cannot_delete(): void
    {
        $entry = $this->makeUser(UserRole::DataEntry);
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $entry->brands()->attach($honda);

        $hondaEnquiry = $this->makeEnquiry($honda, 'honda@example.com');
        $yamahaEnquiry = $this->makeEnquiry($yamaha, 'yamaha@example.com');
        $brandless = $this->makeEnquiry(null, 'general@example.com');

        $this->assertTrue($entry->can('view', $hondaEnquiry));
        $this->assertTrue($entry->can('update', $hondaEnquiry));
        $this->assertFalse($entry->can('delete', $hondaEnquiry));
        $this->assertFalse($entry->can('view', $yamahaEnquiry));
        $this->assertFalse($entry->can('update', $yamahaEnquiry));
        $this->assertFalse($entry->can('view', $brandless));
    }

    public function test_resource_query_only_returns_assigned_brand_enquiries(): void
    {
        $entry = $this->makeUser(UserRole::DataEntry);
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $ducati = $this->makeBrand('ducati');
        $entry->brands()->sync([$honda->id, $yamaha->id]);

        $one = $this->makeEnquiry($honda, 'one@example.com');
        $two = $this->makeEnquiry($yamaha, 'two@example.com');
        $this->makeEnquiry($ducati, 'three@example.com');
        $this->makeEnquiry(null, 'four@example.com');

        $this->actingAs($entry);

        $this->assertEqualsCanonicalizing(
            [$one->id, $two->id],
            EnquiryResource::getEloquentQuery()->pluck('enquiries.id')->all(),
        );
    }

    public function test_authenticated_update_sets_updated_by_without_mass_assignment(): void
    {
        $admin = $this->makeUser(UserRole::Admin);
        $enquiry = $this->makeEnquiry($this->makeBrand('honda'), 'honda@example.com');

        $this->actingAs($admin);
        $enquiry->update(['status' => EnquiryStatus::Contacted]);

        $this->assertSame($admin->id, $enquiry->fresh()->updated_by);
        $this->assertSame(EnquiryStatus::Contacted, $enquiry->fresh()->status);
        $this->assertNotContains('updated_by', $enquiry->getFillable());
    }

    public function test_filament_table_respects_brand_scope(): void
    {
        $entry = $this->makeUser(UserRole::DataEntry);
        $honda = $this->makeBrand('honda');
        $yamaha = $this->makeBrand('yamaha');
        $entry->brands()->attach($honda);
        $accessible = $this->makeEnquiry($honda, 'honda@example.com');
        $hidden = $this->makeEnquiry($yamaha, 'yamaha@example.com');

        Livewire::actingAs($entry)
            ->test(ListEnquiries::class)
            ->assertCanSeeTableRecords([$accessible])
            ->assertCanNotSeeTableRecords([$hidden]);
    }

    public function test_filament_edit_only_changes_status_and_tracks_handler(): void
    {
        $admin = $this->makeUser(UserRole::Admin);
        $enquiry = $this->makeEnquiry($this->makeBrand('honda'), 'honda@example.com');

        Livewire::actingAs($admin)
            ->test(EditEnquiry::class, ['record' => $enquiry->getRouteKey()])
            ->fillForm(['status' => EnquiryStatus::Closed->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(EnquiryStatus::Closed, $enquiry->fresh()->status);
        $this->assertSame($admin->id, $enquiry->fresh()->updated_by);
        $this->assertSame('أحمد محمد', $enquiry->fresh()->name);
    }

    public function test_inactive_staff_cannot_access_enquiries(): void
    {
        $inactive = $this->makeUser(UserRole::DataEntry, false);
        $brand = $this->makeBrand('honda');
        $inactive->brands()->attach($brand);
        $enquiry = $this->makeEnquiry($brand, 'honda@example.com');

        $this->assertFalse($inactive->can('viewAny', Enquiry::class));
        $this->assertFalse($inactive->can('view', $enquiry));
        $this->assertFalse($inactive->can('update', $enquiry));
    }
}
