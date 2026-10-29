<?php

namespace App\Filament\Resources\Motorcycles\Pages;

use App\Filament\Resources\Motorcycles\MotorcycleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMotorcycle extends EditRecord
{
    protected static string $resource = MotorcycleResource::class;
    protected ?bool $hasDatabaseTransactions = true;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        $brandId = (int) $data['brand_id'];

        abort_unless(
            $user?->is_active && (
                $user->isAdmin() ||
                ($user->isDataEntry() && $user->brands()->whereKey($brandId)->exists())
            ),
            403
        );

        return parent::handleRecordUpdate($record, $data);
    }

    protected function afterSave(): void
    {
        $this->record->syncPrimaryImage();
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
    protected function getRedirectUrl(): string
    {
        return MotorcycleResource::getUrl('index');
    }

}
