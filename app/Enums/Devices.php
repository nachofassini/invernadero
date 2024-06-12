<?php

namespace App\Enums;

use App\Enums\Traits\EnumToArray;

enum Devices: string
{
    use EnumToArray;

    case FAN = 'fan';
    case EXTRACTOR = 'extractor';
    case LIGHT = 'light';
    case WATER_PUMP = 'water_pump';
}
