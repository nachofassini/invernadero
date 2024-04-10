<?php

namespace App\Models\Traits;

use App\Models\Activation;
use App\Models\Measure;
use Illuminate\Support\Facades\Artisan;

trait FixPlanDeviations
{
  private function fixLightDeviation(Measure $measure)
  {
    logger('Activando LED para proporcionar luz artificial');
    Artisan::queue('led:switch', ['--turn' => 'on', '--time' => 1, 'cause' => Activation::LOW_LIGHTING, 'measureId' => $measure->id]);
  }

  private function fixTemperatureDeviation(Measure $measure, $deviation)
  {
    if ($deviation['obtained'] < $deviation['expected']) {
      // need to heat up
      if ($measure->outside_temperature > $deviation['obtained']) {
        // it's hotter outside. Use outside temperature air to heat up
        logger('Activando ventilador para calentar usando el aire exterior');
        Artisan::queue('fan:switch', ['--turn' => 'on', '--time' => 0.5, 'measureId' => $measure->id, 'cause' => Activation::LOW_TEMPERATURE]);
      } else {
        // it's colder outside than expected inside. Outside temp won't help. Turn on heater
        logger('Calentar: Calefacción no implementada');
        // Artisan::call('heater:switch', ['--turn' => 'on', '--time' => 1, 'measureId' => $measure->id, 'cause' => Activation::LOW_TEMPERATURE]);
      }
    } else {
      // need to cool down
      if ($measure->outside_temperature < $deviation['obtained']) {
        // it's colder outside. Use outside air temperature to cool down inside
        logger('Activando ventilador para enfriar con el aire fresco del exteriór');
        Artisan::queue('fan:switch', ['--turn' => 'on', '--time' => 0.5, 'measureId' => $measure->id, 'cause' => Activation::HIGH_TEMPERATURE]);
      } else {
        // it's hotter than expected inside. Outside temp won't help. Turn on extractor to remove heat
        logger('Activando extractor para remover el aire caliente');
        Artisan::queue('extractor:switch', ['--turn' => 'on', '--time' => 1, 'measureId' => $measure->id, 'cause' => Activation::HIGH_TEMPERATURE]);
      }
    }
  }

  private function fixHumidityDeviation(Measure $measure, $deviation)
  {
    if ($deviation['obtained'] < $deviation['expected']) {
      // need to increase humidity
      if ($measure->outside_humidity > $deviation['obtained']) {
        // it's more humid outside. Use outside air humidity to increase inside humidity
        logger('Activando ventilador para utilizar el aire humedo del exterior');
        Artisan::queue('fan:switch', ['--turn' => 'on', '--time' => 0.5, 'measureId' => $measure->id, 'cause' => Activation::LOW_HUMIDITY]);
      } else {
        // it's more hum than expected inside. Outside temp won't help. Turn on heater
        logger('Humidificador no implementado');
        // Artisan::queue('extractor:switch', ['--turn' => 'on', '--time' => 1, 'measureId' => $measure->id, 'cause' => Activation::LOW_HUMIDITY]);
      }
    } else {
      // need to decrease humidity
      if ($measure->outside_humidity < $deviation['obtained']) {
        // it's less humid outside. Use outside air humidity to decrease inside humidity

        logger('Activando ventilador para bajar la humedad utilizando el aire seco del exterior');
        Artisan::queue('fan:switch', ['--turn' => 'on', '--time' => 0.5, 'measureId' => $measure->id, 'cause' => Activation::HIGH_HUMIDITY]);

        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        Artisan::queue('extractor:switch', ['--turn' => 'on', '--time' => 1, 'measureId' => $measure->id, 'cause' => Activation::HIGH_HUMIDITY]);
      } else {
        // it's more humid than expected inside. Outside humidity won't help. Turn on extractor to remove humidity
        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        Artisan::queue('extractor:switch', ['--turn' => 'on', '--time' => 1, 'measureId' => $measure->id, 'cause' => Activation::HIGH_HUMIDITY]);
      }
    }
  }

  private function fixSoilHumidityDeviation(Measure $measure)
  {
    logger('Activando riego para proporcionar humedad al suelo');
    Artisan::queue('water:switch', ['--turn' => 'on', '--amount' => 0.1, 'cause' => Activation::LOW_SOIL_HUMIDITY, 'measureId' => $measure->id]);
  }

  private function fixDeviations($deviations, Measure $measure)
  {
    foreach ($deviations as $deviation) {
      // Handle lighting
      if ($deviation['type'] == Activation::LOW_LIGHTING) {
        $this->fixLightDeviation($measure);
      }

      // Handle temperature
      if ($deviation['type'] == Activation::LOW_TEMPERATURE || $deviation['type'] == Activation::HIGH_TEMPERATURE) {
        $this->fixTemperatureDeviation($measure, $deviation);
      }

      // Handle humidity (need to check if wont screw temperature when trying to fix humidity)
      if ($deviation['type'] == Activation::LOW_HUMIDITY || $deviation['type'] == Activation::HIGH_HUMIDITY) {
        $this->fixHumidityDeviation($measure, $deviation);
      }

      // Handle soil humidity
      if ($deviation['type'] == Activation::LOW_SOIL_HUMIDITY) {
        $this->fixSoilHumidityDeviation($measure);
      }
    }
  }
}
