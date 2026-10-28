<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleSpec extends Model
{
    protected $fillable = [
        'motorcycle_id', 'engine_cc', 'engine_type', 'transmission',
        'power_hp', 'torque_nm', 'weight_kg', 'fuel_capacity_liters',
        'seat_height_mm', 'additional_specs',
    ];

    protected function casts(): array
    {
        return [
            'engine_cc' => 'integer',
            'power_hp' => 'decimal:2',
            'torque_nm' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'fuel_capacity_liters' => 'decimal:2',
            'seat_height_mm' => 'integer',
            'additional_specs' => 'array',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
