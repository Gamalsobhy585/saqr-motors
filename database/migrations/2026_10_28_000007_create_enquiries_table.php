<?php

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('motorcycle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('email', 255);
            $table->string('phone', 40)->nullable();

            $table->smallInteger('type')
                ->default(EnquiryType::General->value);

            $table->text('message')->nullable();

            $table->smallInteger('status')
                ->default(EnquiryStatus::New->value);

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['brand_id', 'created_at']);
        });

        DB::statement(
            'ALTER TABLE enquiries
             ADD CONSTRAINT enquiries_type_check
             CHECK (type IN (1, 2, 3))'
        );

        DB::statement(
            'ALTER TABLE enquiries
             ADD CONSTRAINT enquiries_status_check
             CHECK (status IN (1, 2, 3))'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};