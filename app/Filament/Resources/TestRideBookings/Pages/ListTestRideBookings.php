<?php

namespace App\Filament\Resources\TestRideBookings\Pages;

use App\Filament\Resources\TestRideBookings\TestRideBookingResource;
use Carbon\Carbon;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTestRideBookings extends ListRecords
{
    protected static string $resource = TestRideBookingResource::class;

    protected function getHeaderActions(): array
    {
        // No Create, Edit, View, Delete or custom record actions.
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('test_ride_bookings.filters.all')),

            'today' => Tab::make(__('test_ride_bookings.filters.today'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereBetween(
                    'scheduled_at',
                    [today()->startOfDay(), today()->endOfDay()],
                )),

            'week' => Tab::make(__('test_ride_bookings.filters.this_week'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereBetween(
                    'scheduled_at',
                    [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::SUNDAY)],
                )),
        ];
    }
}
