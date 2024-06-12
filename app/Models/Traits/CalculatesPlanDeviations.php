<?php

namespace App\Models\Traits;

use App\Enums\DeviationTypes;
use App\Models\Deviation;
use App\Models\Measure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

const TEMPERATURE_THRESHOLD = 0.1; // 10% of the temperature
const HUMIDITY_THRESHOLD = 0.05; // 5% of the humidity

const DAYLIGHT_THRESHOLD = 0.05; // 5% of the light hours
const MIN_DAYLIGHT_LIGHTNING = 50; // % of lightning to consider it's daylight
const MIN_HOURS_TO_CONSIDER_SUNRISE = 1; // Minimum continuous hours of daylight to consider sunrise had happened

const MIN_SOIL_HUMIDITY = 20; // 20% of the soil humidity

trait CalculatesPlanDeviations
{
    private function getPlanDeviations(Measure $measure): array
    {
        $deviations = [];
        if ($temperatureDeviations = $this->getTemperatureDeviations($measure)) {
            $deviations[] = $temperatureDeviations;
        }
        if ($humidityDeviations = $this->getHumidityDeviations($measure)) {
            $deviations[] = $humidityDeviations;
        }
        if ($lightningDeviations = $this->getLightningDeviations($measure)) {
            $deviations[] = $lightningDeviations;
        }
        if ($soilHumidityDeviations = $this->getSoilHumidityDeviations($measure)) {
            $deviations[] = $soilHumidityDeviations;
        }

        return $deviations;
    }

    private function getTemperatureDeviations(Measure $measure): ?Deviation
    {
        if ($measure->inside_temperature === null || $measure->inside_temperature === 0.0) {
            return null; // ignore bad readings
        }

        $minTemperature = $this->activeStage->min_temperature;
        $maxTemperature = $this->activeStage->max_temperature;

        $temperatureThreshold = ($maxTemperature - $minTemperature) * TEMPERATURE_THRESHOLD;
        $minimumTemperatureLimit = $minTemperature - $temperatureThreshold;
        $maximumTemperatureLimit = $maxTemperature + $temperatureThreshold;

        if ($measure->inside_temperature < $minimumTemperatureLimit) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::LOW_TEMPERATURE->value, 'fix_id' => null],
                ['expected' => $minTemperature, 'observed' => $measure->inside_temperature, 'detection_id' => $measure->id]
            );
        }
        if ($measure->inside_temperature > $maximumTemperatureLimit) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::HIGH_TEMPERATURE->value, 'fix_id' => null],
                ['expected' => $maxTemperature, 'observed' => $measure->inside_temperature, 'detection_id' => $measure->id]
            );
        }

        return null;
    }

    private function getHumidityDeviations(Measure $measure): ?Deviation
    {
        if ($measure->inside_humidity === null || $measure->inside_humidity === 0.0) {
            return null; // ignore bad readings
        }

        $minHumidity = $this->activeStage->min_humidity;
        $maxHumidity = $this->activeStage->max_humidity;

        $humidityThreshold = ($maxHumidity - $minHumidity) * HUMIDITY_THRESHOLD;
        $minimumHumidityLimit = max($minHumidity - $humidityThreshold, 0);
        $maximumHumidityLimit = min($maxHumidity + $humidityThreshold, 100);

        if ($measure->inside_humidity < $minimumHumidityLimit) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::LOW_HUMIDITY->value, 'fix_id' => null],
                ['expected' => $minHumidity, 'observed' => $measure->inside_humidity, 'detection_id' => $measure->id]
            );
        }
        if ($measure->inside_humidity > $maximumHumidityLimit) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::HIGH_HUMIDITY->value, 'fix_id' => null],
                ['expected' => $maxHumidity, 'observed' => $measure->inside_humidity, 'detection_id' => $measure->id]
            );
        }

        return null;
    }

    private function getLightningDeviations(Measure $measure): ?Deviation
    {
        // TODO: Will only work for greenhouse on outside
        $sunriseTime = Deviation::getSunriseTime();
        if (! $sunriseTime) {
            return null;
        }

        $sunriseTime = new Carbon($sunriseTime);
        $localTIme = Date::now();

        if (
            $measure->lighting < MIN_DAYLIGHT_LIGHTNING && // if lightning is lower than expected
            $localTIme->gte($sunriseTime) && // and it's after sunrise
            $sunriseTime->diffInHours($localTIme) < $this->activeStage->light_hours // and it's been less than the expected light hours
        ) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::LOW_LIGHTING->value, 'fix_id' => null],
                ['expected' => MIN_DAYLIGHT_LIGHTNING, 'observed' => $measure->lighting, 'detection_id' => $measure->id]
            );
        }

        return null;
    }

    private function getSoilHumidityDeviations(Measure $measure): ?Deviation
    {
        if ($measure->soil_humidity < MIN_SOIL_HUMIDITY) {
            return Deviation::firstOrCreate(
                ['type' => DeviationTypes::LOW_SOIL_HUMIDITY->value, 'fix_id' => null],
                ['expected' => MIN_SOIL_HUMIDITY, 'observed' => $measure->soil_humidity, 'detection_id' => $measure->id]
            );
        }

        return null;
    }
}
