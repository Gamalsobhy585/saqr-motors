<?php

namespace App\Enums;

enum MotorcycleAvailability: int
{
    case Available = 1;
    case ComingSoon = 2;
    case OutOfStock = 3;
}