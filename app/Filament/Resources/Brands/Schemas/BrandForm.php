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

    /**
     * Strict website URL validation:
     *  1. http/https only, no embedded credentials
     *  2. host is a well-formed domain with a real TLD (no localhost / bare IPs)
     *  3. host resolves in DNS
     *  4. host does not resolve to a private/reserved IP (SSRF protection)
     *
     * Returns the actual validation closure (Filament's ->rule() expects to
     * receive it from a factory closure, see configure()).
     */
    protected static function validateWebsiteUrl(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $invalid = fn () => $fail(__('saqr.brands.website_invalid'));

            $parts = parse_url(trim((string) $value));

            // 1. Scheme + structure
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

            // Support IDN (Arabic / non-ASCII) domains
            $host = strtolower($parts['host']);

            if (function_exists('idn_to_ascii')) {
                $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

                if ($ascii !== false) {
                    $host = $ascii;
                }
            }

            // 2. Well-formed domain with a real TLD
            if (! preg_match('/^(?!-)([a-z0-9-]{1,63}(?<!-)\.)+[a-z]{2,63}$/', $host)) {
                $invalid();

                return;
            }

            // 3. DNS must resolve (trailing dot avoids local search-domain suffixes)
            $fqdn = $host . '.';

            if (
                ! checkdnsrr($fqdn, 'A')
                && ! checkdnsrr($fqdn, 'AAAA')
                && ! checkdnsrr($fqdn, 'CNAME')
            ) {
                $fail(__('saqr.brands.website_unreachable'));

                return;
            }

            // 4. Block private / loopback / reserved IPs
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