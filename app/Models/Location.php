<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'city_id', 'name_ar', 'name_en', 'address_ar', 'address_en',
        'phone', 'google_maps_url',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $location): void {
            // Compatibility with the old required name/city and legacy readers.
            $location->name = $location->name_en;
            $location->address = $location->address_en;
            $location->city = City::query()->findOrFail($location->city_id)->name_en;

            if (! $location->exists && auth()->check()) {
                $location->created_by = auth()->id();
            }
        });
    }

    public function cityRelation(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?: $this->name_en ?: $this->name)
            : ($this->name_en ?: $this->name);
    }

    public function getDisplayAddressAttribute(): ?string
    {
        return app()->getLocale() === 'ar'
            ? ($this->address_ar ?: $this->address_en ?: $this->address)
            : ($this->address_en ?: $this->address);
    }
 

}
