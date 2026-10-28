<?php

namespace App\Models;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    protected $fillable = [
        'brand_id', 'motorcycle_id', 'name', 'email',
        'phone', 'type', 'message', 'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => EnquiryType::class,
            'status' => EnquiryStatus::class,
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
}
