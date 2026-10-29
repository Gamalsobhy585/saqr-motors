<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->persistInSession()
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('services.fields.image'))
                    ->getStateUsing(fn (Service $record): ?string => $record->image_url)
                    ->imageSize(55),

                TextColumn::make('name_ar')
                    ->label(__('services.fields.name_ar'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name_en')
                    ->label(__('services.fields.name_en'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('services.fields.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('services.fields.is_active'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('services.fields.sort_order'))
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('services.fields.created_by'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('services.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label(__('services.fields.is_active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
