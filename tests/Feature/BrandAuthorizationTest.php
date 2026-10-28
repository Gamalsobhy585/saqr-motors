<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(UserRole $role): User
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_admin_can_manage_brands(): void
    {
        $admin = $this->createUser(UserRole::Admin);
        $brand = Brand::factory()->create();

        $this->assertTrue(
            $admin->can('create', Brand::class)
        );

        $this->assertTrue(
            $admin->can('update', $brand)
        );

        $this->assertTrue(
            $admin->can('delete', $brand)
        );
    }

    public function test_data_entry_cannot_manage_brands(): void
    {
        $user = $this->createUser(UserRole::DataEntry);
        $brand = Brand::factory()->create();

        $this->assertFalse(
            $user->can('create', Brand::class)
        );

        $this->assertFalse(
            $user->can('update', $brand)
        );

        $this->assertFalse(
            $user->can('delete', $brand)
        );

        $this->assertTrue(
            $user->can('view', $brand)
        );
    }

    public function test_inactive_admin_cannot_manage_brands(): void
    {
        $admin = $this->createUser(UserRole::Admin);
        $admin->is_active = false;
        $admin->save();

        $this->assertFalse(
            $admin->can('create', Brand::class)
        );
    }
}
