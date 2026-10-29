<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EnquiryType: int implements HasLabel
{
    case General = 1;
    case TestRide = 2;
    case Financing = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::General => __('enquiries.types.general'),
            self::TestRide => __('enquiries.types.test_ride'),
            self::Financing => __('enquiries.types.financing'),
        };
    }
}
