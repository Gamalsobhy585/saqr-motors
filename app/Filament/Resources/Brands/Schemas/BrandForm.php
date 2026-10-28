<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            function (Set $set, ?string $state, string $operation): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->alphaDash()
                        ->unique(
                            table: 'brands',
                            column: 'slug',
                            ignoreRecord: true
                        ),

                    TextInput::make('tagline')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),

                    TextInput::make('website_url')
                        ->label('Website URL')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Brand Media')
                ->schema([
                    FileUpload::make('logo_path')
                        ->label('Brand Logo')
                        ->image()
                        ->disk('public')
                        ->directory('brands/logos')
                        ->maxSize(2048),

                    FileUpload::make('cover_path')
                        ->label('Cover Image')
                        ->image()
                        ->disk('public')
                        ->directory('brands/covers')
                        ->maxSize(5120),
                ])
                ->columns(2),

            Section::make('Visibility & Ordering')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }
}
