<?php

namespace App\Filament\Resources\FinancialPlans;

use App\Filament\Resources\FinancialPlans\Pages\CreateFinancialPlan;
use App\Filament\Resources\FinancialPlans\Pages\EditFinancialPlan;
use App\Filament\Resources\FinancialPlans\Pages\ListFinancialPlans;
use App\Filament\Resources\FinancialPlans\Pages\ViewFinancialPlan;
use App\Filament\Resources\FinancialPlans\Schemas\FinancialPlanForm;
use App\Filament\Resources\FinancialPlans\Schemas\FinancialPlanInfolist;
use App\Filament\Resources\FinancialPlans\Tables\FinancialPlansTable;
use App\Models\FinancialPlan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinancialPlanResource extends Resource
{
    protected static ?string $model = FinancialPlan::class;

    protected static ?string $recordTitleAttribute = 'name_en';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('financial_plans.plural');
    }

    public static function getModelLabel(): string
    {
        return __('financial_plans.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('financial_plans.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return FinancialPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinancialPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancialPlans::route('/'),
            'create' => CreateFinancialPlan::route('/create'),
            'view' => ViewFinancialPlan::route('/{record}'),
            'edit' => EditFinancialPlan::route('/{record}/edit'),
        ];
    }
}
