<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Promotion;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Filament::setCurrentPanel('admin');
    }

    private function user(UserRole $role, bool $active = true): User
    {
        $user = User::factory()->make();
        $user->role = $role;
        $user->is_active = $active;
        $user->save();

        return $user;
    }

    private function brand(string $slug): Brand
    {
        return Brand::query()->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function promotion(Brand $brand): Promotion
    {
        return Promotion::query()->create([
            'brand_id' => $brand->id,
            'title_en' => 'Demo offer',
            'title_ar' => 'عرض تجريبي',
            'discount_percent' => 5,
            'starts_at' => '2026-10-01',
            'ends_at' => '2026-12-31',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_manage_all_promotions(): void
    {
        $admin = $this->user(UserRole::Admin);
        $honda = $this->brand('honda');
        $yamaha = $this->brand('yamaha');
        $hondaPromotion = $this->promotion($honda);
        $yamahaPromotion = $this->promotion($yamaha);

        $this->assertTrue($admin->can('create', Promotion::class));
        $this->assertTrue($admin->can('update', $hondaPromotion));
        $this->assertTrue($admin->can('delete', $yamahaPromotion));

        $this->actingAs($admin);
        $this->assertCount(2, PromotionResource::getEloquentQuery()->get());
    }

    public function test_data_entry_can_only_manage_assigned_brands(): void
    {
        $assigned = $this->brand('honda');
        $other = $this->brand('ducati');
        $assignedPromotion = $this->promotion($assigned);
        $otherPromotion = $this->promotion($other);
        $editor = $this->user(UserRole::DataEntry);
        $editor->brands()->attach($assigned->id);

        $this->assertTrue($editor->can('create', Promotion::class));
        $this->assertTrue($editor->can('view', $assignedPromotion));
        $this->assertTrue($editor->can('update', $assignedPromotion));
        $this->assertTrue($editor->can('delete', $assignedPromotion));
        $this->assertFalse($editor->can('view', $otherPromotion));
        $this->assertFalse($editor->can('update', $otherPromotion));
        $this->assertFalse($editor->can('delete', $otherPromotion));

        $this->actingAs($editor);
        $this->assertEquals([$assignedPromotion->id],
            PromotionResource::getEloquentQuery()->pluck('promotions.id')->all());
    }

    public function test_inactive_and_unassigned_users_cannot_create(): void
    {
        $editor = $this->user(UserRole::DataEntry);
        $this->assertFalse($editor->can('create', Promotion::class));

        $brand = $this->brand('honda');
        $editor->brands()->attach($brand->id);
        $editor->is_active = false;
        $editor->save();

        $this->assertFalse($editor->can('create', Promotion::class));
    }

    public function test_selected_brand_must_belong_to_data_entry(): void
    {
        $assigned = $this->brand('honda');
        $forbidden = $this->brand('ducati');
        $editor = $this->user(UserRole::DataEntry);
        $editor->brands()->attach($assigned->id);
        $this->actingAs($editor);

        $this->expectException(ValidationException::class);
        PromotionResource::validateTarget(['brand_id' => $forbidden->id]);
    }

    public function test_selected_motorcycle_must_match_selected_brand(): void
    {
        $honda = $this->brand('honda');
        $yamaha = $this->brand('yamaha');
        $yamahaBike = Motorcycle::factory()->for($yamaha)->create();
        $admin = $this->user(UserRole::Admin);
        $this->actingAs($admin);

        $this->expectException(ValidationException::class);
        PromotionResource::validateTarget([
            'brand_id' => $honda->id,
            'motorcycle_id' => $yamahaBike->id,
        ]);
    }

    public function test_end_date_cannot_precede_start_date(): void
    {
        $brand = $this->brand('honda');
        $this->actingAs($this->user(UserRole::Admin));

        $this->expectException(ValidationException::class);
        PromotionResource::validateTarget([
            'brand_id' => $brand->id,
            'starts_at' => '2026-12-31',
            'ends_at' => '2026-10-01',
        ]);
    }

    public function test_creator_and_updater_are_recorded(): void
    {
        $brand = $this->brand('honda');
        $admin = $this->user(UserRole::Admin);
        $editor = $this->user(UserRole::DataEntry);
        $editor->brands()->attach($brand->id);

        $this->actingAs($admin);
        $promotion = $this->promotion($brand);
        $this->assertEquals($admin->id, $promotion->created_by);
        $this->assertEquals($admin->id, $promotion->updated_by);

        $this->actingAs($editor);
        $promotion->update(['discount_percent' => 7.50]);
        $this->assertEquals($admin->id, $promotion->fresh()->created_by);
        $this->assertEquals($editor->id, $promotion->fresh()->updated_by);
    }

    public function test_data_entry_can_create_via_filament(): void
    {
        $brand = $this->brand('honda');
        $editor = $this->user(UserRole::DataEntry);
        $editor->brands()->attach($brand->id);
        $this->actingAs($editor);

        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'brand_id' => $brand->id,
                'motorcycle_id' => null,
                'title_en' => 'Weekend special',
                'title_ar' => 'عرض نهاية الأسبوع',
                'description_en' => 'Demo only',
                'description_ar' => 'عرض تجريبي فقط',
                'discount_percent' => 5.00,
                'starts_at' => '2026-10-01',
                'ends_at' => '2026-12-31',
                'is_active' => true,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('promotions', [
            'brand_id' => $brand->id,
            'title_en' => 'Weekend special',
            'created_by' => $editor->id,
        ]);
    }
}
