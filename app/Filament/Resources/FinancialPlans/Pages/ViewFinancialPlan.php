<?php

namespace App\Filament\Resources\FinancialPlans\Pages;

use App\Filament\Resources\FinancialPlans\FinancialPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFinancialPlan extends ViewRecord
{
    protected static string $resource = FinancialPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
