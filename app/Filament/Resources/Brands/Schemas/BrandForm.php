<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Support\DatabaseImages;
use App\Support\ResolvesPublicImages;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                        ->unique(table: 'brands', column: 'slug', ignoreRecord: true),

                    TextInput::make('tagline')
                        ->label(__('saqr.brands.tagline'))
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label(__('saqr.brands.description'))
                        ->rows(4)
                        ->columnSpanFull(),

                    TextInput::make('website_url')
                        ->label(__('saqr.brands.website'))
                        ->placeholder('https://example.com')
                        ->url()
                        ->maxLength(2048)
                        ->rule(fn (): Closure => self::validateWebsiteUrl())
                        ->dehydrateStateUsing(
                            fn (?string $state): ?string => filled($state)
                                ? rtrim(trim($state), '/')
                                : null
                        )
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('saqr.brands.media'))
                ->schema([
                    self::imageUpload('logo_path', 'saqr.brands.logo', 2048),
                    self::imageUpload('cover_path', 'saqr.brands.cover', 5120),
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

    /** Preserve the existing strict website validation logic. */
    protected static function validateWebsiteUrl(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $invalid = fn () => $fail(__('saqr.brands.website_invalid'));
            $parts = parse_url(trim((string) $value));

            if (
                $parts === false
                || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
                || empty($parts['host'])
                || isset($parts['user'])
                || isset($parts['pass'])
            ) {
                $invalid();

                return;
            }

            $host = strtolower($parts['host']);

            if (function_exists('idn_to_ascii')) {
                $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

                if ($ascii !== false) {
                    $host = $ascii;
                }
            }

            if (! preg_match('/^(?!-)([a-z0-9-]{1,63}(?<!-)\.)+[a-z]{2,63}$/', $host)) {
                $invalid();

                return;
            }

            $fqdn = $host . '.';

            if (
                ! checkdnsrr($fqdn, 'A')
                && ! checkdnsrr($fqdn, 'AAAA')
                && ! checkdnsrr($fqdn, 'CNAME')
            ) {
                $fail(__('saqr.brands.website_unreachable'));

                return;
            }

            foreach (gethostbynamel($host) ?: [] as $ip) {
                if (! filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                )) {
                    $invalid();
                    return;
                }
            }
        };
    }

    protected static function imageUpload(string $name, string $labelKey, int $maxSize): FileUpload
    {
        return FileUpload::make($name)
            ->label(__($labelKey))
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            // Livewire's initial temporary upload still uses the configured temp disk.
            // Permanent storage is moved to PostgreSQL on form save.
            ->disk('public')
            ->visibility('public')
            ->imagePreviewHeight('90')
            ->panelLayout('compact')
            ->maxSize($maxSize)
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(ResolvesPublicImages::forDirectory('images/brands'))
            ->saveUploadedFileUsing(
                fn (TemporaryUploadedFile $file): string => DatabaseImages::store($file)
            );
    }
}
