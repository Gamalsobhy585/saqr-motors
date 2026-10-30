<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Models\Brand;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->selectable(false)
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('logo_path')
                    ->label(__('saqr.brands.logo'))
                    ->getStateUsing(fn (Brand $record): ?string => $record->logo_url)
                    ->defaultImageUrl(asset('images/brands/default.png'))
                    ->imageSize(60),

                TextColumn::make('name')
                    ->label(__('saqr.brands.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label(__('saqr.brands.slug'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('tagline')
                    ->label(__('saqr.brands.tagline'))
                    ->limit(35)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('saqr.brands.active'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('saqr.brands.order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('saqr.brands.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('saqr.brands.active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}