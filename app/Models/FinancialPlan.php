<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialPlan extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'down_payment_percent',
        'installment_months',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'down_payment_percent' => 'decimal:2',
            'installment_months' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan): void {
            if (auth()->check()) {
                $plan->created_by = auth()->id();
                $plan->updated_by = auth()->id();
            }
        });

        static::updating(function (self $plan): void {
            if (auth()->check()) {
                $plan->updated_by = auth()->id();
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('ar')
            ? ($this->name_ar ?: $this->name_en)
            : ($this->name_en ?: $this->name_ar);
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->isLocale('ar')
            ? ($this->description_ar ?: $this->description_en)
            : ($this->description_en ?: $this->description_ar);
    }
}
