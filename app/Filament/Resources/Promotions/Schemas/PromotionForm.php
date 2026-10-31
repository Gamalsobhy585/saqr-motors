<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Models\Promotion;
use App\Support\DatabaseImages;
use App\Support\ResolvesPublicImages;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('promotions.details'))
                ->schema([
                    Select::make('brand_id')
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
                        )
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('motorcycle_id', null))
                        ->required(),

                    Select::make('motorcycle_id')
                        ->label(__('promotions.motorcycle'))
                        ->relationship(
                            name: 'motorcycle',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, Get $get): Builder =>
                                $query->where('brand_id', (int) ($get('brand_id') ?: 0)),
                        )
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->disabled(fn (Get $get): bool => blank($get('brand_id')))
                        ->placeholder(__('promotions.all_brand_models')),

                    TextInput::make('title_en')
                        ->label(__('promotions.title_en'))
                        ->required(),

                    TextInput::make('title_ar')
                        ->label(__('promotions.title_ar'))
                        ->required(),

                    Textarea::make('description_en')
                        ->label(__('promotions.description_en'))
                        ->rows(3),

                    Textarea::make('description_ar')
                        ->label(__('promotions.description_ar'))
                        ->rows(3),
                ])->columns(2),

            Section::make(__('promotions.offer'))
                ->schema([
                    TextInput::make('discount_percent')
                        ->label(__('promotions.discount_percent'))
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(99.99)
                        ->step(0.01)
                        ->suffix('%')
                        ->required(),

                    DatePicker::make('starts_at')
                        ->label(__('promotions.starts_at'))
                        ->required()
                        ->native(false),

                    DatePicker::make('ends_at')
                        ->label(__('promotions.ends_at'))
                        ->required()
                        ->native(false)
                        ->minDate(fn (Get $get) => $get('starts_at'))
                        ->rules([
                            fn (Get $get, ?Promotion $record): Closure =>
                                function (
                                    string $attribute,
                                    mixed $value,
                                    Closure $fail
                                ) use ($get, $record): void {
                                    if (! (bool) $get('is_active')) {
                                        return;
                                    }

                                    $brandId = $get('brand_id');
                                    $motorcycleId = $get('motorcycle_id');
                                    $startsAt = $get('starts_at');
                                    $endsAt = $value;

                                    if (blank($motorcycleId)) {
                                        return;
                                    }

                                    if (
                                        blank($brandId)
                                        || blank($startsAt)
                                        || blank($endsAt)
                                        || $endsAt < $startsAt
                                    ) {
                                        return;
                                    }

                                    $overlapExists = Promotion::query()
                                        ->where('brand_id', $brandId)
                                        ->where('motorcycle_id', $motorcycleId)
                                        ->where('is_active', true)
                                        ->whereDate('starts_at', '<=', $endsAt)
                                        ->whereDate('ends_at', '>=', $startsAt)
                                        ->when(
                                            $record,
                                            fn ($query) => $query->whereKeyNot($record->getKey())
                                        )
                                        ->exists();

                                    if ($overlapExists) {
                                        $fail(__('promotions.validation.overlapping_period'));
                                    }
                                },
                        ]),

                    Toggle::make('is_active')
                        ->label(__('promotions.active'))
                        ->default(false),

                    TextInput::make('sort_order')
                        ->label(__('promotions.sort_order'))
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])->columns(2),

            Section::make(__('promotions.media'))
                ->schema([
                    FileUpload::make('image_path')
                        ->label(__('promotions.image'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->visibility('public')
                        ->imagePreviewHeight('90')
                        ->panelLayout('compact')
                        ->maxSize(5120)
                        ->fetchFileInformation(false)
                        ->getUploadedFileUsing(ResolvesPublicImages::forDirectory('images/services'))
                        ->saveUploadedFileUsing(
                            fn (TemporaryUploadedFile $file): string => DatabaseImages::store($file)
                        ),
                ]),
        ]);
    }
}
