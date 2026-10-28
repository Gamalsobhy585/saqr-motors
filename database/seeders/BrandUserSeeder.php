<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BrandUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException(
                'Demo brand assignments are local/testing only.'
            );
        }

        $email = env('SAQR_DATA_ENTRY_EMAIL');

        if (! $email) {
            throw new RuntimeException(
                'SAQR_DATA_ENTRY_EMAIL is required.'
            );
        }

        $user = User::query()
            ->where('email', $email)
            ->firstOrFail();

        if (! $user->isDataEntry()) {
            throw new RuntimeException(
                'Brand assignments require a data-entry user.'
            );
        }

        $brandIds = DB::table('brands')
            ->whereIn('slug', [
                'honda',
                'yamaha',
                'kawasaki',
                'suzuki',
            ])
            ->pluck('id')
            ->all();

        if (count($brandIds) !== 4) {
            throw new RuntimeException(
                'Seed the four required brands first.'
            );
        }

        $user->brands()->syncWithoutDetaching($brandIds);
    }
}
