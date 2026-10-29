<?php

namespace App\Filament\Resources\TestRideBookings;

use App\Filament\Resources\TestRideBookings\Pages\ListTestRideBookings;
use App\Filament\Resources\TestRideBookings\Tables\TestRideBookingsTable;
use App\Models\TestRideBooking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestRideBookingResource extends Resource
{
    protected static ?string $model = TestRideBooking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string
    {
        return __('test_ride_bookings.plural');
    }

    public static function getModelLabel(): string
    {
        return __('test_ride_bookings.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('test_ride_bookings.plural');
    }

    public static function table(Table $table): Table
    {
        return TestRideBookingsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['brand', 'motorcycle', 'location']);

        $user = auth()->user();

        if (! $user || ! $user->is_active) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDataEntry()) {
            // Query-time brand scoping, not just hiding brand filters in the UI.
            return $query->whereIn('brand_id', $user->brands()->select('brands.id'));
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestRideBookings::route('/'),
        ];
    }
}
