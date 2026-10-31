<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use App\Support\ResolvesPublicImages;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $resolve = ResolvesPublicImages::forDirectory('images/brands');

        foreach (['logo_path', 'cover_path'] as $field) {
            $path = $data[$field] ?? null;

            if (blank($path)) {
                continue;
            }

            if (filter_var($path, FILTER_VALIDATE_URL)) {
                continue;
            }

            if ($resolve($path) === null) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return BrandResource::getUrl('index');
    }
}
