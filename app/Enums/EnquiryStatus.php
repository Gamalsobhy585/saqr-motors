<?php

namespace App\Enums;

enum EnquiryStatus: int
{
    case New = 1;
    case Contacted = 2;
    case Closed = 3;
}