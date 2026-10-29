<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('services.sections.details'))
                ->schema([
                    TextEntry::make('name_en')->label(__('services.fields.name_en')),
                    TextEntry::make('name_ar')->label(__('services.fields.name_ar')),
                    TextEntry::make('slug')->label(__('services.fields.slug'))->copyable(),
                    TextEntry::make('sort_order')->label(__('services.fields.sort_order')),
                    TextEntry::make('description_en')
                        ->label(__('services.fields.description_en'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                    TextEntry::make('description_ar')
                        ->label(__('services.fields.description_ar'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('services.sections.media'))
                ->schema([
                    ImageEntry::make('image_path')
                        ->label(__('services.fields.image'))
                        ->getStateUsing(fn (Service $record): ?string => $record->image_url)
                        ->imageHeight(150),
                ]),

            Section::make(__('services.sections.visibility'))
                ->schema([
                    IconEntry::make('is_active')->label(__('services.fields.is_active'))->boolean(),
                    TextEntry::make('creator.name')->label(__('services.fields.created_by'))->placeholder('—'),
                    TextEntry::make('updater.name')->label(__('services.fields.updated_by'))->placeholder('—'),
                    TextEntry::make('created_at')->label(__('services.fields.created_at'))->dateTime(),
                    TextEntry::make('updated_at')->label(__('services.fields.updated_at'))->dateTime(),
                ])
                ->columns(2),
        ]);
    }
}
