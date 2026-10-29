<?php

namespace App\Models;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    protected $fillable = [
        'brand_id',
        'motorcycle_id',
        'name',
        'email',
        'phone',
        'type',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => EnquiryType::class,
            'status' => EnquiryStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $enquiry): void {
            // Only an authenticated Eloquent update stamps the handling user.
            // The attribute is deliberately NOT mass assignable from public requests.
            if (auth()->check() && $enquiry->isDirty()) {
                $enquiry->updated_by = auth()->id();
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
