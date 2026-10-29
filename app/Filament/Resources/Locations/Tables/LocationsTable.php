<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Models\Location;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistSearchInSession()
            ->persistSortInSession()
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name_en')
                    ->label(__('locations.name'))
                    ->formatStateUsing(fn ($state, Location $record): string => $record->display_name)
                    ->searchable(['name_ar', 'name_en', 'name'])
                    ->sortable()->weight('bold'),
                TextColumn::make('cityRelation.name_en')
                    ->label(__('locations.city'))
                    ->formatStateUsing(fn ($state, Location $record): string => $record->cityRelation?->display_name ?? $record->city)
                    ->sortable(),
                TextColumn::make('address_en')
                    ->label(__('locations.address'))
                    ->formatStateUsing(fn ($state, Location $record): ?string => $record->display_address)
                    ->searchable(['address_ar', 'address_en'])
                    ->limit(65),
                TextColumn::make('phone')
                    ->label(__('locations.phone'))
                    ->copyable()
                    ->searchable()
                    ->placeholder('—')
                    ->url(fn (?string $state): ?string => $state ? 'tel:' . $state : null),
                TextColumn::make('google_maps_url')
                    ->label(__('locations.google_maps_url'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('locations.open_map') : '—')
                    ->url(fn (?string $state): ?string => $state, shouldOpenInNewTab: true),
                TextColumn::make('created_at')
                    ->label(__('locations.created_at'))
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label(__('locations.city'))
                    ->relationship('cityRelation', 'name_en')
                    ->searchable()->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
