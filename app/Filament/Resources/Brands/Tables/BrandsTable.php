<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Models\Brand;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([

                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->getStateUsing(
                        fn (Brand $record): ?string => $record->logo_url
                    )
                    ->defaultImageUrl(asset('images/brands/default.png'))
                    ->imageSize(60),


                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('tagline')
                    ->limit(35)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
 
}
