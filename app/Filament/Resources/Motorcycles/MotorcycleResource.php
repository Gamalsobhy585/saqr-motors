<?php

namespace App\Filament\Resources\Motorcycles;

use App\Filament\Resources\Motorcycles\Pages\CreateMotorcycle;
use App\Filament\Resources\Motorcycles\Pages\EditMotorcycle;
use App\Filament\Resources\Motorcycles\Pages\ListMotorcycles;
use App\Filament\Resources\Motorcycles\Pages\ViewMotorcycle;
use App\Filament\Resources\Motorcycles\Schemas\MotorcycleForm;
use App\Filament\Resources\Motorcycles\Tables\MotorcyclesTable;
use App\Models\Motorcycle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MotorcycleResource extends Resource
{
    protected static ?string $model = Motorcycle::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | \BackedEnum | null $navigationIcon = 'lucide-motorbike';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return MotorcycleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotorcyclesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['brand', 'primaryImage']);
        $user = auth()->user();

        if (! $user?->is_active) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereHas(
            'brand.users',
            fn (Builder $brandsUsers) => $brandsUsers->whereKey($user->id)
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMotorcycles::route('/'),
            'create' => CreateMotorcycle::route('/create'),
            'edit' => EditMotorcycle::route('/{record}/edit'),
            'view' => ViewMotorcycle::route('/{record}'),

        ];
    }
    public static function getNavigationLabel(): string
    {
        return __('saqr.motorcycles.plural');
    }

    public static function getModelLabel(): string
    {
        return __('saqr.motorcycles.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('saqr.motorcycles.plural');
    }
    public static function getRecordRouteKeyName(): ?string
    {
        return 'uuid';
    }
}
