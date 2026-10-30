<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function service(array $overrides = []): Service
    {
        return Service::query()->create(array_merge([
            'name_en' => 'Periodic Maintenance',
            'name_ar' => 'الصيانة الدورية',
            'slug' => 'periodic-maintenance',
            'description_en' => 'Test service',
            'description_ar' => 'خدمة تجريبية',
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));
    }

    private function user(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role, 'is_active' => true])->save();

        return $user;
    }

    public function test_guest_sees_only_active_services(): void
    {
        $this->service();
        $this->service([
            'name_en' => 'Hidden Service',
            'name_ar' => 'خدمة مخفية',
            'slug' => 'hidden-service',
            'is_active' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Periodic Maintenance')
            ->assertDontSee('Hidden Service');

    }

    public function test_only_admin_can_mutate_services(): void
    {
        $service = $this->service();
        $admin = $this->user(UserRole::Admin);
        $dataEntry = $this->user(UserRole::DataEntry);

        $this->assertTrue(Gate::forUser($admin)->allows('create', Service::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $service));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $service));

        // Public services are readable on the homepage; admin-panel read permissions
        // remain an independent policy decision.
        $this->assertFalse(Gate::forUser($dataEntry)->allows('create', Service::class));
        $this->assertFalse(Gate::forUser($dataEntry)->allows('update', $service));
        $this->assertFalse(Gate::forUser($dataEntry)->allows('delete', $service));
    }

    public function test_creator_and_updater_are_recorded(): void
    {
        $admin = $this->user(UserRole::Admin);
        $this->actingAs($admin);
        $service = $this->service();

        $this->assertSame($admin->id, $service->created_by);
        $this->assertNull($service->updated_by);

        $service->update(['name_en' => 'Updated Maintenance']);

        $this->assertSame($admin->id, $service->fresh()->updated_by);
    }
}
