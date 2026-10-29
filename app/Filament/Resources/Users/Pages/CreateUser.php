<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = new User();

        $user->fill(
            Arr::only($data, [
                'name',
                'email',
                'password',
            ])
        );

        $user->role = UserRole::from($data['role']);
        $user->is_active = (bool) $data['is_active'];

        $user->save();

        return $user;
    }
    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
    
}
