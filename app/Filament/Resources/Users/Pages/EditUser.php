<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        $record->fill(
            Arr::only($data, [
                'name',
                'email',
                'password',
            ])
        );

        $record->role = UserRole::from($data['role']);
        $record->is_active = (bool) $data['is_active'];

        $record->save();

        return $record;
    }
}
