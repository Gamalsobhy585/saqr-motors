<?php

namespace App\Filament\Resources\Motorcycles\Pages;

use App\Filament\Resources\Motorcycles\MotorcycleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMotorcycle extends CreateRecord
{
    protected static string $resource = MotorcycleResource::class;
        protected ?bool $hasDatabaseTransactions = true;


    protected function handleRecordCreation(array $data): Model
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

        return parent::handleRecordCreation($data);
    }

    protected function afterCreate(): void
    {
        $this->record->syncPrimaryImage();
    }
    protected function getRedirectUrl(): string
    {
        return MotorcycleResource::getUrl('index');
    }

}
