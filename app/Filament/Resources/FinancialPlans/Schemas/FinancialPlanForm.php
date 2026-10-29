<?php

namespace App\Filament\Resources\FinancialPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinancialPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('financial_plans.details'))
                ->schema([
                    TextInput::make('name_en')
                        ->label(__('financial_plans.name_en'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_ar')
                        ->label(__('financial_plans.name_ar'))
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description_en')
                        ->label(__('financial_plans.description_en'))
                        ->rows(3),

                    Textarea::make('description_ar')
                        ->label(__('financial_plans.description_ar'))
                        ->rows(3),
                ])
                ->columns(2),

            Section::make(__('financial_plans.terms'))
                ->schema([
                    TextInput::make('down_payment_percent')
                        ->label(__('financial_plans.down_payment_percent'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99.99)
                        ->step(0.01)
                        ->suffix('%')
                        ->required(),

                    TextInput::make('installment_months')
                        ->label(__('financial_plans.installment_months'))
                        ->integer()
                        ->minValue(1)
                        ->maxValue(120)
                        ->suffix(__('financial_plans.months_suffix'))
                        ->required(),

                    Toggle::make('is_active')
                        ->label(__('financial_plans.active'))
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label(__('financial_plans.sort_order'))
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }
}
