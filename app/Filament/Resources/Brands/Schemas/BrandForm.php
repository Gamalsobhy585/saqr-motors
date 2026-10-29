<?php

namespace App\Filament\Resources\Brands\Schemas;

use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Dns;


class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('saqr.brands.information'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('saqr.brands.name'))
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
                        ->label(__('saqr.brands.slug'))
                        ->required()
                        ->maxLength(255)
                        ->alphaDash()
                        ->unique(
                            table: 'brands',
                            column: 'slug',
                            ignoreRecord: true
                        ),

                    TextInput::make('tagline')
                        ->label(__('saqr.brands.tagline'))
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label(__('saqr.brands.description'))
                        ->rows(4)
                        ->columnSpanFull(),

                    TextInput::make('website_url')
                        ->label(__('saqr.brands.website'))
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('saqr.brands.media'))
                ->schema([
                    self::imageUpload('logo_path', 'saqr.brands.logo', 'brands/logos', 2048),
                    self::imageUpload('cover_path', 'saqr.brands.cover', 'brands/covers', 5120),
                ])
                ->columns(2),

            Section::make(__('saqr.brands.visibility'))
                ->schema([
                    Toggle::make('is_active')
                        ->label(__('saqr.brands.active'))
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label(__('saqr.brands.order'))
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    protected static function imageUpload(
        string $name,
        string $labelKey,
        string $directory,
        int $maxSize
    ): FileUpload {
        return FileUpload::make($name)
            ->label(__($labelKey))
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->imagePreviewHeight('90')
            ->panelLayout('compact')
            ->maxSize($maxSize)
            ->fetchFileInformation(false)
            ->preventFilePathTampering()
            ->getUploadedFileUsing(self::resolveStoredFile());
    }

    /**
     * Resolves both seeded static images (public/images/brands/*)
     * and files uploaded through Filament (public disk).
     */
    protected static function resolveStoredFile(): Closure
    {
        return function (string $file): ?array {
            // Seeded images living in public/images/brands
            if (str_starts_with($file, 'images/brands/')) {
                $path = public_path($file);

                if (! is_file($path)) {
                    return null;
                }

                return [
                    'name' => basename($file),
                    'size' => filesize($path),
                    'type' => mime_content_type($path),
                    'url'  => asset($file),
                ];
            }

            // Images uploaded through Filament
            $disk = Storage::disk('public');

            if (! $disk->exists($file)) {
                return null;
            }

            return [
                'name' => basename($file),
                'size' => $disk->size($file),
                'type' => $disk->mimeType($file),
                'url'  => $disk->url($file),
            ];
        };
    }
}