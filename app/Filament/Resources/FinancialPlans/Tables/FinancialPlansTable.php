<?php

namespace App\Filament\Resources\FinancialPlans\Tables;

use App\Models\FinancialPlan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FinancialPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make(app()->isLocale('ar') ? 'name_ar' : 'name_en')
                    ->label(__('financial_plans.name'))
                    ->searchable(['name_ar', 'name_en'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('down_payment_percent')
                    ->label(__('financial_plans.down_payment_percent'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('installment_months')
                    ->label(__('financial_plans.installment_months'))
                    ->suffix(' ' . __('financial_plans.months_suffix'))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('financial_plans.active'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('financial_plans.sort_order'))
                    ->sortable(),

                TextColumn::make('updatedBy.name')
                    ->label(__('financial_plans.updated_by'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('financial_plans.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('financial_plans.active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
