<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = UserRole::Admin;
        $user->is_active = true;
        $user->save();

        return $user;
    }

    private function dataEntry(): User
    {
        $user = User::factory()->create();
        $user->role = UserRole::DataEntry;
        $user->is_active = true;
        $user->save();

        return $user;
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->admin();
        $employee = $this->dataEntry();

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$admin, $employee]);
    }

    public function test_data_entry_cannot_access_user_management(): void
    {
        $employee = $this->dataEntry();

        Livewire::actingAs($employee)
            ->test(ListUsers::class)
            ->assertForbidden();

        Livewire::actingAs($employee)
            ->test(CreateUser::class)
            ->assertForbidden();
    }

    public function test_admin_can_create_data_entry_with_brand_assignments(): void
    {
        $admin = $this->admin();
        $brand = Brand::query()->create([
            'name' => 'Honda',
            'slug' => 'honda',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'SAQR Editor',
                'email' => 'editor@example.test',
                'role' => UserRole::DataEntry->value,
                'is_active' => true,
                'brands' => [$brand->id],
                'password' => 'Safe!R8n4Q2x',
                'password_confirmation' => 'Safe!R8n4Q2x',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = User::query()->where('email', 'editor@example.test')->firstOrFail();

        $this->assertTrue($created->isDataEntry());
        $this->assertTrue($created->brands()->whereKey($brand->id)->exists());
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Safe!R8n4Q2x', $created->password));
    }

    public function test_admin_can_update_another_users_name(): void
    {
        $admin = $this->admin();
        $employee = $this->dataEntry();

        Livewire::actingAs($admin)
            ->test(EditUser::class, ['record' => $employee->getRouteKey()])
            ->fillForm(['name' => 'Updated Editor'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated Editor', $employee->fresh()->name);
    }

    public function test_data_entry_cannot_edit_another_user(): void
    {
        $employee = $this->dataEntry();
        $other = $this->dataEntry();

        Livewire::actingAs($employee)
            ->test(EditUser::class, ['record' => $other->getRouteKey()])
            ->assertForbidden();
    }
}
