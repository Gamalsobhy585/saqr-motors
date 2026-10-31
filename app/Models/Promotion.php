<?php

namespace App\Models;

use App\Models\Concerns\TracksEditors;
use App\Support\PublicImageUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use TracksEditors;

    protected $fillable = [
        'brand_id', 'motorcycle_id', 'title_en', 'title_ar',
        'description_en', 'description_ar', 'discount_percent',
        'starts_at', 'ends_at', 'image_path', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->whereDate('starts_at', '<=', $today)
            ->whereDate('ends_at', '>=', $today);
    }

    public function getLocalizedTitleAttribute(): string
    {
        return app()->isLocale('ar')
            ? ($this->title_ar ?: $this->title_en)
            : ($this->title_en ?: $this->title_ar);
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->isLocale('ar')
            ? ($this->description_ar ?: $this->description_en)
            : ($this->description_en ?: $this->description_ar);
    }

    public function getImageUrlAttribute(): ?string
    {
        // Preserve the existing static services fallback for seeded images.
        return PublicImageUrls::resolve($this->image_path, 'images/services');
    }
}
