<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EnquiryStatus: int implements HasLabel, HasColor
{
    case New = 1;
    case Contacted = 2;
    case Closed = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::New => __('enquiries.statuses.new'),
            self::Contacted => __('enquiries.statuses.contacted'),
            self::Closed => __('enquiries.statuses.closed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Contacted => 'info',
            self::Closed => 'success',
        };
    }
}
