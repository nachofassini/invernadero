<?php

namespace App\Enums;

use App\Enums\Traits\EnumToArray;

enum DeviationTypes: string
{
    use EnumToArray;

    case LOW_TEMPERATURE = 'low_temperature';
    case HIGH_TEMPERATURE = 'high_temperature';
    case LOW_HUMIDITY = 'low_humidity';
    case HIGH_HUMIDITY = 'high_humidity';
    case LOW_SOIL_HUMIDITY = 'low_soil_humidity';
    case HIGH_SOIL_HUMIDITY = 'high_soil_humidity';
    case LOW_CO2 = 'low_co2';
    case HIGH_CO2 = 'high_co2';
    case LOW_LIGHTING = 'low_lighting';
}
