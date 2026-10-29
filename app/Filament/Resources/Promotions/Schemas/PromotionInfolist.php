<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Models\Promotion;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromotionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('promotions.details'))
                ->schema([
                    TextEntry::make('title_en')
                        ->label(__('promotions.title'))
                        ->formatStateUsing(fn (Promotion $record): string => $record->localized_title)
                        ->weight('bold'),

                    TextEntry::make('brand.name')->label(__('promotions.brand')),

                    TextEntry::make('motorcycle.name')
                        ->label(__('promotions.motorcycle'))
                        ->placeholder(__('promotions.all_brand_models')),

                    TextEntry::make('description_en')
                        ->label(__('promotions.description'))
                        ->formatStateUsing(fn (Promotion $record): ?string => $record->localized_description)
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('promotions.offer'))
                ->schema([
                    TextEntry::make('discount_percent')
                        ->label(__('promotions.discount_percent'))
                        ->suffix('%'),

                    TextEntry::make('starts_at')
                        ->label(__('promotions.starts_at'))
                        ->date(),

                    TextEntry::make('ends_at')
                        ->label(__('promotions.ends_at'))
                        ->date(),

                    IconEntry::make('is_active')
                        ->label(__('promotions.active'))
                        ->boolean(),

                    TextEntry::make('sort_order')
                        ->label(__('promotions.sort_order')),
                ])->columns(2),

            Section::make(__('promotions.media'))
                ->schema([
                    ImageEntry::make('image_path')
                        ->label(__('promotions.image'))
                        ->getStateUsing(fn (Promotion $record): ?string => $record->image_url)
                        ->imageHeight(150),
                ]),

            Section::make(__('promotions.audit'))
                ->schema([
                    TextEntry::make('createdBy.name')
                        ->label(__('promotions.created_by'))
                        ->placeholder('—'),

                    TextEntry::make('updatedBy.name')
                        ->label(__('promotions.updated_by'))
                        ->placeholder('—'),

                    TextEntry::make('created_at')
                        ->label(__('promotions.created_at'))
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('promotions.updated_at'))
                        ->dateTime(),
                ])->columns(2),
        ]);
    }
}
