<?php

namespace App\Filament\Resources\TestRideBookings\Tables;

use App\Models\TestRideBooking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestRideBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->recordUrl(null)
            ->recordActions([])
            ->columns([
                TextColumn::make('customer_name')
                    ->label(__('test_ride_bookings.customer_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label(__('test_ride_bookings.phone'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('email')
                    ->label(__('test_ride_bookings.email'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('brand.name')
                    ->label(__('test_ride_bookings.brand'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('motorcycle.name')
                    ->label(__('test_ride_bookings.motorcycle'))
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('location.name')
                    ->label(__('test_ride_bookings.location'))
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('scheduled_at')
                    ->label(__('test_ride_bookings.scheduled_at'))
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('notes')
                    ->label(__('test_ride_bookings.notes'))
                    ->placeholder('—')
                    ->limit(65)
                    ->tooltip(fn (TestRideBooking $record): ?string => $record->notes)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label(__('test_ride_bookings.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

               
            ]);
    }
}
