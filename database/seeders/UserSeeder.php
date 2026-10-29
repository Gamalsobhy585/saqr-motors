<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException(
                'Demo user seeding is restricted to local/testing.'
            );
        }

        $users = [
            [
                'name' => 'SAQR Admin',
                'email' => env('SAQR_ADMIN_EMAIL'),
                'password' => env('SAQR_ADMIN_PASSWORD'),
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'SAQR Data Entry',
                'email' => env('SAQR_DATA_ENTRY_EMAIL'),
                'password' => env('SAQR_DATA_ENTRY_PASSWORD'),
                'role' => UserRole::DataEntry,
            ],
        ];

        foreach ($users as $data) {
            Validator::make($data, [
                'email' => ['required', 'email'],
                'password' => [
                    'required',
                    Password::min(12)
                        ->letters()
                        ->numbers()
                        ->symbols(),
                ],
            ])->validate();

            $user = User::firstOrNew([
                'email' => $data['email'],
            ]);

            if ($user->exists) {
                // Preserve existing passwords, roles and status.
                continue;
            }

            $user->name = $data['name'];
            $user->password = Hash::make($data['password']);
            $user->role = $data['role'];
            $user->is_active = true;
            $user->save();
        }
    }
}
