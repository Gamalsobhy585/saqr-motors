<?php

namespace App\Filament\Resources\Promotions\Tables;

use App\Models\Promotion;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistSearchInSession()
            ->persistSortInSession()
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('promotions.image'))
                    ->getStateUsing(fn (Promotion $record): ?string => $record->image_url)
                    ->imageSize(56),

                TextColumn::make('title_en')
                    ->label(__('promotions.title'))
                    ->formatStateUsing(fn (Promotion $record): string => $record->localized_title)
                    ->searchable(['title_en', 'title_ar'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('brand.name')
                    ->label(__('promotions.brand'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('motorcycle.name')
                    ->label(__('promotions.motorcycle'))
                    ->placeholder(__('promotions.all_brand_models'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('discount_percent')
                    ->label(__('promotions.discount_percent'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label(__('promotions.starts_at'))
                    ->date()
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label(__('promotions.ends_at'))
                    ->date()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('promotions.active'))
                    ->boolean(),

                TextColumn::make('updatedBy.name')
                    ->label(__('promotions.updated_by'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('promotions.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label(__('promotions.brand'))
                    ->relationship(
                        name: 'brand',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $user = auth()->user();

                            if (! $user?->isAdmin()) {
                                $query->whereHas('users',
                                    fn (Builder $users) => $users->whereKey($user?->id ?? 0),
                                );
                            }

                            return $query;
                        },
                    )->searchable()->preload(),

                TernaryFilter::make('is_active')
                    ->label(__('promotions.active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
