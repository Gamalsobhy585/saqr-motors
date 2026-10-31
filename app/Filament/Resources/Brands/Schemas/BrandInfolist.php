<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Models\Brand;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('saqr.brands.information'))
                ->schema([
                    TextEntry::make('name')
                        ->label(__('saqr.brands.name'))
                        ->weight('bold'),

                    TextEntry::make('slug')
                        ->label(__('saqr.brands.slug'))
                        ->copyable(),

                    TextEntry::make('tagline')
                        ->label(__('saqr.brands.tagline'))
                        ->placeholder('-'),

                    TextEntry::make('website_url')
                        ->label(__('saqr.brands.website'))
                        ->url(fn (?string $state): ?string => $state, shouldOpenInNewTab: true)
                        ->placeholder('-'),

                    TextEntry::make('description')
                        ->label(__('saqr.brands.description'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('saqr.brands.media'))
                ->schema([
                    ImageEntry::make('logo_path')
                        ->label(__('saqr.brands.logo'))
                        ->getStateUsing(fn (Brand $record): ?string => $record->logo_url)
                        ->defaultImageUrl(asset('images/brands/default.png'))
                        ->imageSize(120),

                    ImageEntry::make('cover_path')
                        ->label(__('saqr.brands.cover'))
                        ->getStateUsing(fn (Brand $record): ?string => $record->cover_url)
                        ->imageHeight(150),
                ])
                ->columns(2),

            Section::make(__('saqr.brands.visibility'))
                ->schema([
                    IconEntry::make('is_active')
                        ->label(__('saqr.brands.active'))
                        ->boolean(),

                    TextEntry::make('sort_order')
                        ->label(__('saqr.brands.order')),

                    TextEntry::make('created_at')
                        ->label(__('saqr.brands.created_at'))
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('saqr.brands.updated_at'))
                        ->dateTime(),
                ])
                ->columns(2),
        ]);
    }
}
