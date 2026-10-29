<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('enquiries.processing'))
                ->description(__('enquiries.processing_hint'))
                ->schema([
                    Select::make('status')
                        ->label(__('enquiries.status'))
                        ->options(EnquiryStatus::class)
                        ->native(false)
                        ->required(),
                ]),

            Section::make(__('enquiries.customer_details'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('enquiries.name'))
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('email')
                        ->label(__('enquiries.email'))
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('phone')
                        ->label(__('enquiries.phone'))
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('type')
                        ->label(__('enquiries.type'))
                        ->options(EnquiryType::class)
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make(__('enquiries.request_details'))
                ->schema([
                    Select::make('brand_id')
                        ->label(__('enquiries.brand'))
                        ->relationship('brand', 'name')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('motorcycle_id')
                        ->label(__('enquiries.motorcycle'))
                        ->relationship('motorcycle', 'name')
                        ->disabled()
                        ->dehydrated(false),

                    Textarea::make('message')
                        ->label(__('enquiries.message'))
                        ->rows(5)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
