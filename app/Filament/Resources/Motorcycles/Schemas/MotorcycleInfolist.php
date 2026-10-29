<?php

namespace App\Filament\Resources\Motorcycles\Schemas;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MotorcycleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Motorcycle Details
            Section::make(__('saqr.motorcycles.details'))
                ->schema([
                    TextEntry::make('brand.name')
                        ->label(__('saqr.motorcycles.brand')),

                    TextEntry::make('name')
                        ->label(__('saqr.motorcycles.name'))
                        ->weight('bold'),

                    TextEntry::make('slug')
                        ->label(__('saqr.motorcycles.slug'))
                        ->copyable(),

                    TextEntry::make('category')
                        ->label(__('saqr.motorcycles.category'))
                        ->badge()
                        ->formatStateUsing(fn (MotorcycleCategory $state): string => match ($state) {
                            MotorcycleCategory::Urban => __('saqr.categories.urban'),
                            MotorcycleCategory::Adventure => __('saqr.categories.adventure'),
                            MotorcycleCategory::Performance => __('saqr.categories.performance'),
                            MotorcycleCategory::Premium => __('saqr.categories.premium'),
                        }),

                    TextEntry::make('model_year')
                        ->label(__('saqr.motorcycles.model_year'))
                        ->placeholder('-'),

                    TextEntry::make('starting_price')
                        ->label(__('saqr.motorcycles.price'))
                        ->formatStateUsing(fn (?string $state, $record): string =>
                            $state === null ? '—' : number_format((float) $state, 2) . ' ' . $record->currency_code
                        ),

                    TextEntry::make('availability_status')
                        ->label(__('saqr.motorcycles.availability'))
                        ->badge()
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

                    TextEntry::make('sort_order')
                        ->label(__('saqr.motorcycles.order')),

                    TextEntry::make('description')
                        ->label(__('saqr.motorcycles.description'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // Publishing
            Section::make(__('saqr.motorcycles.publishing'))
                ->schema([
                    IconEntry::make('is_published')
                        ->label(__('saqr.motorcycles.published'))
                        ->boolean(),

                    IconEntry::make('is_featured')
                        ->label(__('saqr.motorcycles.featured'))
                        ->boolean(),

                    TextEntry::make('created_at')
                        ->label(__('saqr.motorcycles.created_at'))
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('saqr.motorcycles.updated_at'))
                        ->dateTime(),
                ])
                ->columns(4),

            // Technical Specifications
            Section::make(__('saqr.motorcycles.specifications'))
                ->schema([
                    TextEntry::make('specification.engine_cc')
                        ->label(__('saqr.motorcycles.engine_cc'))
                        ->suffix(' cc')
                        ->placeholder('-'),

                    TextEntry::make('specification.engine_type')
                        ->label(__('saqr.motorcycles.engine_type'))
                        ->placeholder('-'),

                    TextEntry::make('specification.transmission')
                        ->label(__('saqr.motorcycles.transmission'))
                        ->placeholder('-'),

                    TextEntry::make('specification.power_hp')
                        ->label(__('saqr.motorcycles.power_hp'))
                        ->suffix(' hp')
                        ->placeholder('-'),

                    TextEntry::make('specification.torque_nm')
                        ->label(__('saqr.motorcycles.torque_nm'))
                        ->suffix(' Nm')
                        ->placeholder('-'),

                    TextEntry::make('specification.weight_kg')
                        ->label(__('saqr.motorcycles.weight_kg'))
                        ->suffix(' kg')
                        ->placeholder('-'),

                    TextEntry::make('specification.fuel_capacity_liters')
                        ->label(__('saqr.motorcycles.fuel_capacity'))
                        ->suffix(' L')
                        ->placeholder('-'),

                    TextEntry::make('specification.seat_height_mm')
                        ->label(__('saqr.motorcycles.seat_height'))
                        ->suffix(' mm')
                        ->placeholder('-'),

                    KeyValueEntry::make('specification.additional_specs')
                        ->label(__('saqr.motorcycles.additional_specs'))
                        ->keyLabel(__('saqr.motorcycles.spec_key'))
                        ->valueLabel(__('saqr.motorcycles.spec_value'))
                        ->columnSpanFull(),
                ])
                ->columns(4)
                ->collapsible(),

            // Image Gallery
            Section::make(__('saqr.motorcycles.gallery'))
                ->schema([
                    ImageEntry::make('images.path')
                        ->label(__('saqr.motorcycles.images'))
                        ->disk('public')
                        ->imageHeight(140)
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }
}