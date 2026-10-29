<?php

namespace App\Models;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use App\Models\Concerns\TracksEditors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Motorcycle extends Model
{
    use HasFactory, SoftDeletes, TracksEditors,HasUuids;

    protected $fillable = [
        'brand_id', 'name', 'slug', 'category', 'description',
        'model_year', 'starting_price', 'currency_code',
        'availability_status', 'is_featured', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => MotorcycleCategory::class,
            'availability_status' => MotorcycleAvailability::class,
            'model_year' => 'integer',
            'starting_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function specification(): HasOne
    {
        return $this->hasOne(MotorcycleSpec::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(MotorcycleImage::class)->where('is_primary', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** After Filament has saved related images, make the first image primary. */
    public function syncPrimaryImage(): void
    {
        DB::transaction(function (): void {
            $this->images()->where('is_primary', true)->update(['is_primary' => false]);

            $first = $this->images()->orderBy('sort_order')->orderBy('id')->first();

            if ($first) {
                $first->is_primary = true;
                $first->save();
            }
        });
    }
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

}
