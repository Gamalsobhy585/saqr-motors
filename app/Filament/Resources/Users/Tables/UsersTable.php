<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('saqr.users.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('saqr.users.email'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label(__('saqr.users.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => self::roleLabel($state))
                    ->color(fn ($state): string => self::roleColor($state))
                    ->sortable(),

                TextColumn::make('brands.name')
                    ->label(__('saqr.users.brands'))
                    ->badge()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('saqr.users.active'))
                    ->boolean(),

                TextColumn::make('email_verified_at')
                    ->label(__('saqr.users.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('saqr.users.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('saqr.users.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('saqr.users.role'))
                    ->options([
                        UserRole::Admin->value => __('saqr.roles.admin'),
                        UserRole::DataEntry->value => __('saqr.roles.data_entry'),
                    ]),

                TernaryFilter::make('is_active')
                    ->label(__('saqr.users.active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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