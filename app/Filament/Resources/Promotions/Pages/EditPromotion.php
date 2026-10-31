<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Support\MissingImagePaths;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Matches PromotionForm's existing static-image fallback directory.
        return MissingImagePaths::clear($data, 'image_path', 'images/services');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return PromotionResource::validateTarget($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return PromotionResource::getUrl('index');
    }
}
