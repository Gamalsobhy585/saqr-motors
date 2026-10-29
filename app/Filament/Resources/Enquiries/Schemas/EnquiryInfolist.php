<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('enquiries.customer_details'))
                ->schema([
                    TextEntry::make('name')
                        ->label(__('enquiries.name'))
                        ->weight('bold'),

                    TextEntry::make('email')
                        ->label(__('enquiries.email'))
                        ->copyable()
                        ->url(fn (?string $state): ?string =>
                            $state ? 'mailto:' . $state : null),

                    TextEntry::make('phone')
                        ->label(__('enquiries.phone'))
                        ->copyable()
                        ->url(fn (?string $state): ?string =>
                            $state ? 'tel:' . preg_replace('/[^+0-9]/', '', $state) : null),
                ])
                ->columns(2),

            Section::make(__('enquiries.request_details'))
                ->schema([
                    TextEntry::make('brand.name')
                        ->label(__('enquiries.brand'))
                        ->placeholder('—'),

                    TextEntry::make('motorcycle.name')
                        ->label(__('enquiries.motorcycle'))
                        ->placeholder('—'),

                    TextEntry::make('type')
                        ->label(__('enquiries.type'))
                        ->badge(),

                    TextEntry::make('status')
                        ->label(__('enquiries.status'))
                        ->badge(),

                    TextEntry::make('message')
                        ->label(__('enquiries.message'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('enquiries.history'))
                ->schema([
                    TextEntry::make('created_at')
                        ->label(__('enquiries.created_at'))
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('enquiries.updated_at'))
                        ->dateTime(),

                    TextEntry::make('updatedBy.name')
                        ->label(__('enquiries.updated_by'))
                        ->placeholder(__('enquiries.not_handled_yet')),
                ])
                ->columns(3),
        ]);
    }
}
