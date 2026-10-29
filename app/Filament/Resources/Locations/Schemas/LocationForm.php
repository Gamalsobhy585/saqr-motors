<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('locations.details'))
                ->schema([
                    Select::make('city_id')
                        ->label(__('locations.city'))
                        ->relationship('cityRelation', 'name_en')
                        ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),

                    TextInput::make('name_ar')
                        ->label(__('locations.name_ar'))
                        ->required()->maxLength(255),

                    TextInput::make('name_en')
                        ->label(__('locations.name_en'))
                        ->required()->maxLength(255),

                    Textarea::make('address_ar')
                        ->label(__('locations.address_ar'))
                        ->rows(2)->required()->columnSpanFull(),

                    Textarea::make('address_en')
                        ->label(__('locations.address_en'))
                        ->rows(2)->required()->columnSpanFull(),

                    TextInput::make('phone')
                        ->label(__('locations.phone'))
                        ->tel()
                        ->prefix('+20')
                        ->placeholder('1012345678')
                        // Local portion only, after +20. Egyptian mobile prefixes: 10,11,12,15.
                        ->rule('regex:/^1[0125][0-9]{8}$/')
                        ->maxLength(10)
                        ->formatStateUsing(fn (?string $state): ?string =>
                            $state && str_starts_with($state, '+20') ? substr($state, 3) : $state
                        )
                        ->dehydrateStateUsing(fn (?string $state): ?string =>
                            filled($state) ? '+20' . $state : null
                        )
                        ->helperText(__('locations.phone_hint')),

                    TextInput::make('google_maps_url')
                        ->label(__('locations.google_maps_url'))
                        ->url()->maxLength(2048)
                        ->placeholder('https://www.google.com/maps/search/?api=1&query=...')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }
}
