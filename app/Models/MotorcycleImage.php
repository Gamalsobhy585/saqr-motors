<?php

namespace App\Models;

use App\Support\PublicImageUrls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleImage extends Model
{
    protected $fillable = [
        'motorcycle_id', 'path', 'alt_text', 'is_primary', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return PublicImageUrls::resolve($this->path, 'images/motorcycles');
    }
}
