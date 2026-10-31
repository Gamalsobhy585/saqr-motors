<?php

namespace App\Models;

use App\Models\Concerns\TracksEditors;
use App\Support\PublicImageUrls;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory, TracksEditors;

    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'logo_path',
        'cover_path', 'website_url', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function motorcycles(): HasMany
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return PublicImageUrls::resolve($this->logo_path, 'images/brands');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return PublicImageUrls::resolve($this->cover_path, 'images/brands');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
