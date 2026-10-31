<?php

namespace App\Filament\Resources\Motorcycles\Tables;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Motorcycle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MotorcyclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('primaryImage.path')
                    ->label(__('saqr.motorcycles.photo'))
                    ->getStateUsing(fn (Motorcycle $record): ?string => $record->primary_image_url)
                    ->imageSize(56),

                TextColumn::make('name')
                    ->label(__('saqr.motorcycles.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('brand.name')
                    ->label(__('saqr.motorcycles.brand'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('model_year')
                    ->label(__('saqr.motorcycles.model_year'))
                    ->sortable(),

                TextColumn::make('category')
                    ->label(__('saqr.motorcycles.category'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (MotorcycleCategory $state): string => match ($state) {
                        MotorcycleCategory::Urban => __('saqr.categories.urban'),
                        MotorcycleCategory::Adventure => __('saqr.categories.adventure'),
                        MotorcycleCategory::Performance => __('saqr.categories.performance'),
                        MotorcycleCategory::Premium => __('saqr.categories.premium'),
                    }),

                TextColumn::make('starting_price')
                    ->label(__('saqr.motorcycles.price'))
                    ->formatStateUsing(fn (?string $state, $record): string =>
                        $state === null ? '—' : number_format((float) $state, 2) . ' ' . $record->currency_code
                    )
                    ->sortable(),

                TextColumn::make('availability_status')
                    ->label(__('saqr.motorcycles.availability'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (MotorcycleAvailability $state): string => match ($state) {
                        MotorcycleAvailability::Available => __('saqr.availability.available'),
                        MotorcycleAvailability::ComingSoon => __('saqr.availability.coming_soon'),
                        MotorcycleAvailability::OutOfStock => __('saqr.availability.out_of_stock'),
                    })
                    ->color(fn (MotorcycleAvailability $state): string => match ($state) {
                        MotorcycleAvailability::Available => 'success',
                        MotorcycleAvailability::ComingSoon => 'warning',
                        MotorcycleAvailability::OutOfStock => 'danger',
                    }),

                IconColumn::make('is_published')
                    ->label(__('saqr.motorcycles.published'))
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label(__('saqr.motorcycles.featured'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('saqr.motorcycles.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label(__('saqr.motorcycles.brand'))
                    ->relationship(
                        'brand',
                        'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $user = auth()->user();

                            if (! $user?->isAdmin()) {
                                $query->whereHas(
                                    'users',
                                    fn (Builder $q) => $q->whereKey($user?->id ?? 0)
                                );
                            }

                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload(),

                Filter::make('price_range')
                    ->label(__('saqr.motorcycles.price'))
                    ->schema([
                        TextInput::make('min_price')
                            ->label(__('saqr.motorcycles.min_price'))
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0'),

                        TextInput::make('max_price')
                            ->label(__('saqr.motorcycles.max_price'))
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(
                        fn (Builder $query, array $data): Builder =>
                            $query
                                ->when(
                                    filled($data['min_price'] ?? null),
                                    fn (Builder $q) => $q->where(
                                        'starting_price', '>=', $data['min_price']
                                    )
                                )
                                ->when(
                                    filled($data['max_price'] ?? null),
                                    fn (Builder $q) => $q->where(
                                        'starting_price', '<=', $data['max_price']
                                    )
                                )
                    ),

                SelectFilter::make('availability_status')
                    ->label(__('saqr.motorcycles.availability'))
                    ->options([
                        MotorcycleAvailability::Available->value => __('saqr.availability.available'),
                        MotorcycleAvailability::ComingSoon->value => __('saqr.availability.coming_soon'),
                        MotorcycleAvailability::OutOfStock->value => __('saqr.availability.out_of_stock'),
                    ]),

                SelectFilter::make('category')
                    ->label(__('saqr.motorcycles.category'))
                    ->options([
                        MotorcycleCategory::Urban->value => __('saqr.categories.urban'),
                        MotorcycleCategory::Adventure->value => __('saqr.categories.adventure'),
                        MotorcycleCategory::Performance->value => __('saqr.categories.performance'),
                        MotorcycleCategory::Premium->value => __('saqr.categories.premium'),
                    ]),

                TernaryFilter::make('is_published')
                    ->label(__('saqr.motorcycles.published')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
