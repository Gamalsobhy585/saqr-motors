<?php

namespace App\Models;

use App\Enums\MotorcycleAvailability;
use App\Enums\MotorcycleCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Motorcycle extends Model
{
    use SoftDeletes;

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function specs(): HasOne
    {
        return $this->hasOne(MotorcycleSpec::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }
}
