<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make(__('saqr.users.information'))
                ->schema([
                    TextEntry::make('name')
                        ->label(__('saqr.users.name'))
                        ->weight('bold'),

                    TextEntry::make('email')
                        ->label(__('saqr.users.email'))
                        ->copyable(),

                    TextEntry::make('role')
                        ->label(__('saqr.users.role'))
                        ->badge()
                        ->formatStateUsing(fn ($state): string => self::roleLabel($state))
                        ->color(fn ($state): string => self::roleColor($state)),

                    IconEntry::make('is_active')
                        ->label(__('saqr.users.active'))
                        ->boolean(),
                ])
                ->columns(2),

            Section::make(__('saqr.users.assignments'))
                ->schema([
                    TextEntry::make('brands.name')
                        ->label(__('saqr.users.brands'))
                        ->badge()
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make(__('saqr.users.account_details'))
                ->schema([
                    TextEntry::make('email_verified_at')
                        ->label(__('saqr.users.email_verified_at'))
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('created_at')
                        ->label(__('saqr.users.created_at'))
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label(__('saqr.users.updated_at'))
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columns(3),
        ]);
    }

    private static function resolveRole(mixed $state): ?UserRole
    {
        return $state instanceof UserRole ? $state : UserRole::tryFrom($state);
    }

    private static function roleLabel(mixed $state): string
    {
        return match (self::resolveRole($state)) {
            UserRole::Admin => __('saqr.roles.admin'),
            UserRole::DataEntry => __('saqr.roles.data_entry'),
            default => (string) $state,
        };
    }

    private static function roleColor(mixed $state): string
    {
        return match (self::resolveRole($state)) {
            UserRole::Admin => 'danger',
            UserRole::DataEntry => 'info',
            default => 'gray',
        };
    }
}