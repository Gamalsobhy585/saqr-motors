<?php

namespace App\Filament\Resources\FinancialPlans\Pages;

use App\Filament\Resources\FinancialPlans\FinancialPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialPlan extends EditRecord
{
    protected static string $resource = FinancialPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return FinancialPlanResource::getUrl('index');
    }
}
