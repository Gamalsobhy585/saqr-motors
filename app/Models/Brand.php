<?php

namespace App\Models;

use App\Models\Concerns\TracksEditors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Support\DatabaseImages;
class Brand extends Model
{
    use HasFactory,TracksEditors;


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
        $path = $this->logo_path;

        if (blank($path)) {
            return null;
        }
        if (str_starts_with($path, 'db-media/')) {
            return DatabaseImages::url($path);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return is_file(public_path($path))
                ? asset($path)
                : null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
