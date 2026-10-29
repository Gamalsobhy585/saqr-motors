<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The previous Locations module has already created `locations`.
        Schema::table('locations', function (Blueprint $table): void {
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->text('address_en')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->restrictOnDelete();
        });

        // Preserve existing locations and populate what can be inferred safely.
        // New locations will be written with BOTH bilingual fields and legacy columns.
        DB::table('locations')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                DB::table('locations')->where('id', $row->id)->update([
                    'name_ar' => $row->name,
                    'name_en' => $row->name,
                    'address_ar' => $row->address,
                    'address_en' => $row->address,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['name_ar','name_en','address_ar','address_en']);
        });
    }
};
