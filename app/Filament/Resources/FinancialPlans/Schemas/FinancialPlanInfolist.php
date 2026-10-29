<?php

namespace App\Filament\Resources\FinancialPlans\Schemas;

use App\Models\FinancialPlan;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('financial_plans.details'))
                ->schema([
                    TextEntry::make('name_en')
                        ->label(__('financial_plans.name'))
                        ->formatStateUsing(fn (FinancialPlan $record): string => $record->localized_name)
                        ->weight('bold'),

                    TextEntry::make('down_payment_percent')
                        ->label(__('financial_plans.down_payment_percent'))
                        ->suffix('%'),

                    TextEntry::make('installment_months')
                        ->label(__('financial_plans.installment_months'))
                        ->suffix(' ' . __('financial_plans.months_suffix')),

                    TextEntry::make('description_en')
                        ->label(__('financial_plans.description'))
                        ->formatStateUsing(fn (FinancialPlan $record): ?string => $record->localized_description)
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('financial_plans.management'))
                ->schema([
                    IconEntry::make('is_active')
                        ->label(__('financial_plans.active'))
                        ->boolean(),

                    TextEntry::make('sort_order')
                        ->label(__('financial_plans.sort_order')),

                    TextEntry::make('createdBy.name')
                        ->label(__('financial_plans.created_by'))
                        ->placeholder('—'),

                    TextEntry::make('updatedBy.name')
                        ->label(__('financial_plans.updated_by'))
                        ->placeholder('—'),

                    TextEntry::make('created_at')
                        ->label(__('financial_plans.created_at'))
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label(__('financial_plans.updated_at'))
                        ->dateTime(),
                ])
                ->columns(2),
        ]);
    }
}
