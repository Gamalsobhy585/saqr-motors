<?php

namespace App\Filament\Resources\Motorcycles\Schemas;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use App\Support\ResolvesPublicImages;
use App\Support\DatabaseImages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
class MotorcycleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Motorcycle Details
            Section::make(__('saqr.motorcycles.details'))
                ->schema([
                    Select::make('brand_id')
                        ->label(__('saqr.motorcycles.brand'))
                        ->relationship(
                            name: 'brand',
                            titleAttribute: 'name',
                            modifyQueryUsing: function (
                                Builder $query
                            ): Builder {
                                $user = auth()->user();

                                if (! $user?->isAdmin()) {
                                    $query->whereHas(
                                        'users',
                                        fn (Builder $q) =>
                                            $q->whereKey(
                                                $user?->id ?? 0
                                            )
                                    );
                                }

                                return $query;
                            }
                        )
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),

                    TextInput::make('name')
                        ->label(__('saqr.motorcycles.name'))
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            function (
                                Set $set,
                                ?string $state,
                                string $operation
                            ): void {
                                if ($operation === 'create') {
                                    $set(
                                        'slug',
                                        Str::slug($state ?? '')
                                    );
                                }
                            }
                        ),

                    TextInput::make('slug')
                        ->label(__('saqr.motorcycles.slug'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(180)
                        ->unique(
                            table: 'motorcycles',
                            column: 'slug',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (
                                Unique $rule,
                                Get $get
                            ): Unique => $rule->where(
                                'brand_id',
                                $get('brand_id')
                            )
                        ),

                    Select::make('category')
                        ->label(__('saqr.motorcycles.category'))
                        ->options([
                            MotorcycleCategory::Urban->value =>
                                __('saqr.categories.urban'),

                            MotorcycleCategory::Adventure->value =>
                                __('saqr.categories.adventure'),

                            MotorcycleCategory::Performance->value =>
                                __('saqr.categories.performance'),

                            MotorcycleCategory::Premium->value =>
                                __('saqr.categories.premium'),
                        ])
                        ->native(false)
                        ->required(),

                    TextInput::make('model_year')
                        ->label(__('saqr.motorcycles.model_year'))
                        ->integer()
                        ->minValue(1900)
                        ->maxValue((int) date('Y') + 1),

                    TextInput::make('starting_price')
                        ->label(__('saqr.motorcycles.price'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('currency_code')
                        ->label(__('saqr.motorcycles.currency'))
                        ->default('EGP')
                        ->required()
                        ->length(3)
                        ->formatStateUsing(
                            fn (?string $state): string =>
                                strtoupper($state ?? 'EGP')
                        ),

                    Select::make('availability_status')
                        ->label(__('saqr.motorcycles.availability'))
                        ->options([
                            MotorcycleAvailability::Available->value =>
                                __('saqr.availability.available'),

                            MotorcycleAvailability::ComingSoon->value =>
                                __('saqr.availability.coming_soon'),

                            MotorcycleAvailability::OutOfStock->value =>
                                __('saqr.availability.out_of_stock'),
                        ])
                        ->default(
                            MotorcycleAvailability::ComingSoon->value
                        )
                        ->native(false)
                        ->required(),

                    Textarea::make('description')
                        ->label(__('saqr.motorcycles.description'))
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_featured')
                        ->label(__('saqr.motorcycles.featured'))
                        ->default(false),

                    Toggle::make('is_published')
                        ->label(__('saqr.motorcycles.published'))
                        ->default(false),

                    TextInput::make('sort_order')
                        ->label(__('saqr.motorcycles.order'))
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),

            // Technical Specifications
            Section::make(__('saqr.motorcycles.specifications'))
                ->relationship('specification')
                ->schema([
                    TextInput::make('engine_cc')
                        ->label(__('saqr.motorcycles.engine_cc'))
                        ->integer()
                        ->minValue(0),

                    TextInput::make('engine_type')
                        ->label(__('saqr.motorcycles.engine_type'))
                        ->maxLength(255),

                    TextInput::make('transmission')
                        ->label(__('saqr.motorcycles.transmission'))
                        ->maxLength(255),

                    TextInput::make('power_hp')
                        ->label(__('saqr.motorcycles.power_hp'))
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('torque_nm')
                        ->label(__('saqr.motorcycles.torque_nm'))
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('weight_kg')
                        ->label(__('saqr.motorcycles.weight_kg'))
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('fuel_capacity_liters')
                        ->label(__('saqr.motorcycles.fuel_capacity'))
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('seat_height_mm')
                        ->label(__('saqr.motorcycles.seat_height'))
                        ->integer()
                        ->minValue(0),

                    KeyValue::make('additional_specs')
                        ->label(
                            __('saqr.motorcycles.additional_specs')
                        )
                        ->keyLabel(
                            __('saqr.motorcycles.spec_key')
                        )
                        ->valueLabel(
                            __('saqr.motorcycles.spec_value')
                        )
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible(),

            // Image Gallery
Section::make(__('saqr.motorcycles.gallery'))
    ->description(__('saqr.motorcycles.gallery_hint'))
    ->schema([
        Repeater::make('images')
            ->label(__('saqr.motorcycles.images'))
            ->relationship()
            ->orderColumn('sort_order')
            ->defaultItems(0)
            ->addActionLabel(__('saqr.motorcycles.add_image'))
            ->minItems(1)
            ->maxItems(3)
            ->schema([
                
      FileUpload::make('path')
        ->label(__('saqr.motorcycles.image'))
        ->image()
        ->acceptedFileTypes([
            'image/jpeg',
            'image/png',
            'image/webp',
        ])
        ->disk('public')
        ->directory('motorcycles/images')
        ->visibility('public')
        ->imagePreviewHeight('90')
        ->panelLayout('compact')
        ->maxSize(5120)
        ->required()
        ->fetchFileInformation(false)
        ->getUploadedFileUsing(
            ResolvesPublicImages::forDirectory('images/motorcycles')
        )
        ->saveUploadedFileUsing(
            fn (TemporaryUploadedFile $file): string => DatabaseImages::store($file)
        ),

                        TextInput::make('alt_text')
                            ->label(__('saqr.motorcycles.alt_text'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->collapsible(),

        ]);
    }
}
