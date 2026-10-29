<?php

namespace App\Filament\Resources\Enquiries\Tables;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistSearchInSession()
            ->persistSortInSession()
            ->columns([
                TextColumn::make('name')
                    ->label(__('enquiries.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label(__('enquiries.phone'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('email')
                    ->label(__('enquiries.email'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('brand.name')
                    ->label(__('enquiries.brand'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('motorcycle.name')
                    ->label(__('enquiries.motorcycle'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->label(__('enquiries.type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('enquiries.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('updatedBy.name')
                    ->label(__('enquiries.updated_by'))
                    ->placeholder(__('enquiries.not_handled_yet'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('enquiries.created_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('enquiries.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label(__('enquiries.brand'))
                    ->relationship(
                        'brand',
                        'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $user = auth()->user();

                            if (! $user?->isAdmin()) {
                                $query->whereHas(
                                    'users',
                                    fn (Builder $users) => $users->whereKey($user?->id ?? 0),
                                );
                            }

                            return $query;
                        },
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label(__('enquiries.type'))
                    ->options(EnquiryType::class),

                SelectFilter::make('status')
                    ->label(__('enquiries.status'))
                    ->options(EnquiryStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
