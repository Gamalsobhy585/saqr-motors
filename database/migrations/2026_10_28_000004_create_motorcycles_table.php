<?php

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('slug', 180);

            $table->smallInteger('category')
                ->default(MotorcycleCategory::Urban->value);

            $table->text('description')->nullable();
            $table->unsignedSmallInteger('model_year')->nullable();
            $table->decimal('starting_price', 12, 2)->nullable();
            $table->char('currency_code', 3)->default('EGP');

            $table->smallInteger('availability_status')
                ->default(MotorcycleAvailability::Available->value);

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['brand_id', 'slug']);
            $table->index(['is_published', 'is_featured', 'sort_order']);
            $table->index(['brand_id', 'category']);
        });

        DB::statement(
            'ALTER TABLE motorcycles
             ADD CONSTRAINT motorcycles_category_check
             CHECK (category IN (1, 2, 3, 4))'
        );

        DB::statement(
            'ALTER TABLE motorcycles
             ADD CONSTRAINT motorcycles_availability_check
             CHECK (availability_status IN (1, 2, 3))'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};