<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;

class EditProfile extends BaseEditProfile
{
    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->disabled(fn (): bool => ! $this->getUser()->isAdmin());
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->disabled(fn (): bool => ! $this->getUser()->isAdmin());
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! $this->getUser()->isAdmin()) {
            unset($data['name'], $data['email']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}