<?php

namespace App\Enums;

enum EnquiryType: int
{
    case General = 1;
    case TestRide = 2;
    case Financing = 3;
}