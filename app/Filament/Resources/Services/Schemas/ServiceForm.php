<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('services.sections.details'))
                ->schema([
                    TextInput::make('name_en')
                        ->label(__('services.fields.name_en'))
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        }),

                    TextInput::make('name_ar')
                        ->label(__('services.fields.name_ar'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label(__('services.fields.slug'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(160)
                        ->unique(table: 'services', column: 'slug', ignoreRecord: true),

                    TextInput::make('sort_order')
                        ->label(__('services.fields.sort_order'))
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),

                    Textarea::make('description_en')
                        ->label(__('services.fields.description_en'))
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('description_ar')
                        ->label(__('services.fields.description_ar'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('services.sections.media'))
                ->schema([
                    FileUpload::make('image_path')
                        ->label(__('services.fields.image'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->directory('services/images')
                        ->visibility('public')
                        ->imagePreviewHeight('90')
                        ->panelLayout('compact')
                        ->maxSize(5120),
                ]),

            Section::make(__('services.sections.visibility'))
                ->schema([
                    Toggle::make('is_active')
                        ->label(__('services.fields.is_active'))
                        ->default(true),
                ]),
        ]);
    }
}
