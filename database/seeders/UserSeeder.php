<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $allowed = app()->environment(['local', 'testing'])
            || config('saqr_public.allow_demo_seeding') === true;

        if (! $allowed) {
            throw new RuntimeException(
                'Demo user seeding is disabled in this environment.'
            );
        }

        $users = [
            [
                'name' => 'SAQR Admin',
                'email' => config('saqr_public.admin_email'),
                'password' => config('saqr_public.admin_password'),
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'SAQR Data Entry',
                'email' => config('saqr_public.data_entry_email'),
                'password' => config('saqr_public.data_entry_password'),
                'role' => UserRole::DataEntry,
            ],
        ];

        foreach ($users as $data) {
            Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => [
                    'required',
                    Password::min(8)
                        ->letters()
                        ->numbers()
                        ->symbols(),
                ],
            ])->validate();

            $user = User::query()->firstOrNew([
                'email' => $data['email'],
            ]);

            if ($user->exists) {
                // Never overwrite existing passwords, roles or status.
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
