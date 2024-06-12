<?php

namespace App\Enums;

use App\Enums\Traits\EnumToArray;

enum MeasureUnits: string
{
    use EnumToArray;

    case MILLIMETERS = 'mm3';
    case CUBIC_METERS = 'm3';
    case PERCENTAGE = '%';
    case HOURS = 'Hs.';
    case MINUTES = 'Mins.';
    case PARTS_PER_MILLION = 'ppm';
    case CELSIUS = 'ºC';
}
