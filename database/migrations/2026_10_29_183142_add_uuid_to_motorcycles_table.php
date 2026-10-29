<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')->unique();
        });

        DB::table('motorcycles')
            ->whereNull('uuid')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($motorcycles) {
                foreach ($motorcycles as $motorcycle) {
                    DB::table('motorcycles')
                        ->where('id', $motorcycle->id)
                        ->update(['uuid' => (string) Str::uuid()]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};