<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\FinancialPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FinancialPlanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function plan(array $attributes = []): FinancialPlan
    {
        return FinancialPlan::create(array_merge([
            'name_en' => 'Starter Plan',
            'name_ar' => 'الخطة الاقتصادية',
            'down_payment_percent' => 20,
            'installment_months' => 12,
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    public function test_guests_only_see_active_financial_plans(): void
    {
        $this->plan();
        $this->plan(['name_en' => 'Hidden Plan', 'name_ar' => 'خطة مخفية', 'is_active' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Starter Plan')
            ->assertDontSee('Hidden Plan');
    }

    public function test_only_active_admin_can_manage_financial_plans(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);
        $dataEntry = User::factory()->create(['role' => UserRole::DataEntry, 'is_active' => true]);
        $plan = $this->plan();

        $this->assertTrue(Gate::forUser($admin)->allows('create', FinancialPlan::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $plan));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $plan));
        $this->assertFalse(Gate::forUser($dataEntry)->allows('viewAny', FinancialPlan::class));
        $this->assertFalse(Gate::forUser($dataEntry)->allows('update', $plan));
    }

    public function test_created_and_updated_by_are_stamped_for_authenticated_admin(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);
        $this->actingAs($admin);

        $plan = $this->plan();
        $this->assertEquals($admin->id, $plan->created_by);
        $this->assertEquals($admin->id, $plan->updated_by);

        $plan->update(['installment_months' => 18]);
        $this->assertEquals($admin->id, $plan->fresh()->updated_by);
    }
}
