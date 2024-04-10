<?php

namespace App\Models\Traits;

use App\Models\Activation;
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
  private function getTemperatureDeviations(Measure $measure)
  {
    $minTemperature = $this->activeStage->min_temperature;
    $maxTemperature = $this->activeStage->max_temperature;

    $temperatureThreshold = ($maxTemperature - $minTemperature) * TEMPERATURE_THRESHOLD;
    $minimumTemperatureLimit = $minTemperature - $temperatureThreshold;
    $maximumTemperatureLimit = $maxTemperature + $temperatureThreshold;

    if ($measure->inside_temperature < $minimumTemperatureLimit) {
      logger("Temperature is lower than expected. Expected: {$minTemperature}. Obtained: $measure->inside_temperature");
      return ['type' => Activation::LOW_TEMPERATURE, 'expected' => $minTemperature, 'obtained' => $measure->inside_temperature];
    }
    if ($measure->inside_temperature > $maximumTemperatureLimit) {
      logger("Temperature is higher than expected. Expected: {$maxTemperature}. Obtained: $measure->inside_temperature");
      return ['type' => Activation::HIGH_TEMPERATURE, 'expected' => $minTemperature, 'obtained' => $measure->inside_temperature];
    }
  }

  private function getHumidityDeviations(Measure $measure)
  {
    $minHumidity = $this->activeStage->min_humidity;
    $maxHumidity = $this->activeStage->max_humidity;

    $humidityThreshold = ($maxHumidity - $minHumidity) * HUMIDITY_THRESHOLD;
    $minimumHumidityLimit = max($minHumidity - $humidityThreshold, 0);
    $maximumHumidityLimit = min($maxHumidity + $humidityThreshold, 100);

    if ($measure->inside_humidity < $minimumHumidityLimit) {
      logger("Humidity is lower than expected. Expected: {$minHumidity}. Obtained: $measure->inside_humidity");
      return ['type' => Activation::LOW_HUMIDITY, 'expected' => $minHumidity, 'obtained' => $measure->inside_humidity];
    }
    if ($measure->inside_humidity > $maximumHumidityLimit) {
      logger("Humidity is higher than expected. Expected: {$maxHumidity}. Obtained: $measure->inside_humidity");
      return ['type' => Activation::HIGH_HUMIDITY, 'expected' => $maxHumidity, 'obtained' => $measure->inside_humidity];
    }
  }

  private function getSoilHumidityDeviations(Measure $measure)
  {
    $minSoilHumidity = MIN_SOIL_HUMIDITY;

    if ($measure->soil_humidity < $minSoilHumidity) {
      logger("Soil humidity is lower than expected. Expected: {$minSoilHumidity}. Obtained: $measure->soil_humidity");
      return ['type' => Activation::LOW_SOIL_HUMIDITY, 'expected' => $minSoilHumidity, 'obtained' => $measure->soil_humidity];
    }
  }

  private function getLightningDeviations(Measure $measure)
  {
    // TODO: Will only work for greenhouse on outside
    $sunriseTime = Activation::getSunriseTime();
    if (!$sunriseTime) return;

    $sunriseTime = new Carbon($sunriseTime);
    $localTIme = Date::now();

    if ($measure->lighting < MIN_DAYLIGHT_LIGHTNING && $sunriseTime->diffInHours($localTIme) < $this->activeStage->light_hours) {
      logger("Lightning is lower than expected. Expected > " . MIN_DAYLIGHT_LIGHTNING . ". Obtained: $measure->lighting");
      return ['type' => Activation::LOW_LIGHTING, 'expected' => MIN_DAYLIGHT_LIGHTNING, 'obtained' => $measure->lighting];
    }

    // Metodo viejo. 
    // A partir del amanecer, contar cuantas horas continuas tuvo iluminación > 50 (al menos el 95% de las mediciones, para evitar q alguna sombra que nos tape 10mins se detecte como q no hubo luz)
    /* $lastMeasuresForStageRequiredLightHours = Measure::whereBetween('created_at', [$sunriseTime, Date::now()])
            ->selectRaw('IF(lighting > 50, 1, 0) AS isDayLight')
            ->get();
        $lastMeasuresWithDaylight = $lastMeasuresForStageRequiredLightHours->where('isDayLight', 1);
        $percentageOfMeasuresWithDaylight = ($lastMeasuresWithDaylight->count() / $lastMeasuresForStageRequiredLightHours->count()) * 100;

        logger([
            'lastMeasuresForStageRequiredLightHours' => $lastMeasuresForStageRequiredLightHours->count(),
            'lastMeasuresWithDaylight' => $lastMeasuresWithDaylight->count(),
            'percentageOfMeasuresWithDaylight' => $percentageOfMeasuresWithDaylight,
        ]); */
  }

  private function getPlanDeviations(Measure $measure)
  {
    $deviations = [];
    if ($temperatureDeviations = $this->getTemperatureDeviations($measure)) $deviations[] = $temperatureDeviations;
    if ($humidityDeviations = $this->getHumidityDeviations($measure)) $deviations[] = $humidityDeviations;
    if ($lightningDeviations = $this->getLightningDeviations($measure)) $deviations[] = $lightningDeviations;
    if ($soilHumidityDeviations = $this->getSoilHumidityDeviations($measure)) $deviations[] = $soilHumidityDeviations;
    return $deviations;
  }
}
