<?php

namespace App\Filament\Resources\FinancialPlans\Pages;

use App\Filament\Resources\FinancialPlans\FinancialPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinancialPlans extends ListRecords
{
    protected static string $resource = FinancialPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
