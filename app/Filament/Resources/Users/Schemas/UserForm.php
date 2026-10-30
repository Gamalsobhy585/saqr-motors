<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Account Information
            Section::make(__('saqr.users.information'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('saqr.users.name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label(__('saqr.users.email'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('role')
                        ->label(__('saqr.users.role'))
                        ->options([
                            UserRole::Admin->value =>
                                __('saqr.roles.admin'),

                            UserRole::DataEntry->value =>
                                __('saqr.roles.data_entry'),
                        ])
                        ->default(UserRole::DataEntry->value)
                        ->required()
                        ->native(false),

                    Toggle::make('is_active')
                        ->label(__('saqr.users.active'))
                        ->default(true),
                ])
                ->columns(2),

            // Brand Assignments
            Section::make(__('saqr.users.assignments'))
                ->schema([
                    Select::make('brands')
                        ->label(__('saqr.users.brands'))
                        ->relationship('brands', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->helperText(
                            __('saqr.users.brands_hint')
                        )
                        ->columnSpanFull(),
                ]),

            // Password
            Section::make(__('saqr.users.password_section'))
                ->schema([
                  TextInput::make('password')
                    ->label(__('saqr.users.password'))
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->rules([
                        Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised(),
                    ])
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(__('saqr.users.password_hint')),

                    TextInput::make('password_confirmation')
                        ->label(__('saqr.users.confirm_password'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false),
                ])
                ->columns(2),

        ]);
    }
}
