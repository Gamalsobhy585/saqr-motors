<?php

namespace App\Filament\Resources\Promotions;

use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Resources\Promotions\Pages\ViewPromotion;
use App\Filament\Resources\Promotions\Schemas\PromotionForm;
use App\Filament\Resources\Promotions\Schemas\PromotionInfolist;
use App\Filament\Resources\Promotions\Tables\PromotionsTable;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Promotion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $recordTitleAttribute = 'title_en';

    protected static string|\BackedEnum|null $navigationIcon = 'lucide-badge-percent';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('promotions.plural');
    }

    public static function getModelLabel(): string
    {
        return __('promotions.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('promotions.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return PromotionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PromotionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromotionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['brand', 'motorcycle', 'createdBy', 'updatedBy']);

        $user = auth()->user();

        if (! $user?->is_active) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        if (! $user->isDataEntry()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('brand.users',
            fn (Builder $users) => $users->whereKey($user->id),
        );
    }

    /**
     * Authorize the submitted foreign keys, not just the dropdown choices.
     * Called by both Create and Edit pages after normal Filament form validation.
     */
    public static function validateTarget(array $data): array
    {
        $user = auth()->user();
        $brandId = (int) ($data['brand_id'] ?? 0);

        $allowed = $user?->is_active && (
            ($user->isAdmin() && Brand::query()->whereKey($brandId)->exists()) ||
            ($user->isDataEntry() && $user->brands()->whereKey($brandId)->exists())
        );

        if (! $allowed) {
            throw ValidationException::withMessages([
                'data.brand_id' => __('promotions.validation.brand_not_allowed'),
            ]);
        }

        if (filled($data['motorcycle_id'] ?? null) && ! Motorcycle::query()
            ->whereKey($data['motorcycle_id'])
            ->where('brand_id', $brandId)
            ->exists()) {
            throw ValidationException::withMessages([
                'data.motorcycle_id' => __('promotions.validation.motorcycle_brand_mismatch'),
            ]);
        }

        if (filled($data['starts_at'] ?? null) && filled($data['ends_at'] ?? null)
            && (string) $data['ends_at'] < (string) $data['starts_at']) {
            throw ValidationException::withMessages([
                'data.ends_at' => __('promotions.validation.invalid_date_range'),
            ]);
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'view' => ViewPromotion::route('/{record}'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }
}
