<?php

namespace App\Models\Traits;

use App\Jobs\ActivateDevice;
use App\Models\Activation;
use App\Models\Measure;

trait FixPlanDeviations
{
  private function fixLightDeviation(Measure $measure)
  {
    logger('Activando LED para proporcionar luz artificial');
    ActivateDevice::dispatch(Activation::DEVICE_LIGHT, Activation::LOW_LIGHTING, 1, $measure->id);
  }

  private function fixTemperatureDeviation(Measure $measure, $deviation)
  {
    if ($deviation['obtained'] < $deviation['expected']) {
      // need to heat up
      $activationCause = Activation::LOW_TEMPERATURE;
      if ($measure->outside_temperature > $deviation['obtained']) {
        // it's hotter outside. Use outside temperature air to heat up
        logger('Activando ventilador para calentar usando el aire exterior');
        ActivateDevice::dispatch(Activation::DEVICE_FAN, $activationCause, 0.5, $measure->id);
      } else {
        // it's colder outside than expected inside. Outside temp won't help. Turn on heater
        logger('Calentar: Calefacción no implementada');
        // ActivateDevice::dispatch(Activation::DEVICE_HEATER, $activationCause, 1, $measure->id);
      }
    } else {
      // need to cool down
      $activationCause = Activation::HIGH_TEMPERATURE;
      if ($measure->outside_temperature < $deviation['obtained']) {
        // it's colder outside. Use outside air temperature to cool down inside
        logger('Activando ventilador para enfriar con el aire fresco del exteriór');
        ActivateDevice::dispatch(Activation::DEVICE_FAN, $activationCause, 0.5, $measure->id);
      } else {
        // it's hotter than expected inside. Outside temp won't help. Turn on extractor to remove heat
        logger('Activando extractor para remover el aire caliente');
        ActivateDevice::dispatch(Activation::DEVICE_EXTRACTOR, $activationCause, 0.5, $measure->id);
      }
    }
  }

  private function fixHumidityDeviation(Measure $measure, $deviation)
  {
    if ($deviation['obtained'] < $deviation['expected']) {
      // need to increase humidity
      $activationCause = Activation::LOW_HUMIDITY;
      if ($measure->outside_humidity > $deviation['obtained']) {
        // it's more humid outside. Use outside air humidity to increase inside humidity
        logger('Activando ventilador para utilizar el aire humedo del exterior');
        ActivateDevice::dispatch(Activation::DEVICE_FAN, $activationCause, 0.5, $measure->id);
      } else {
        // it's more hum than expected inside. Outside temp won't help. Turn on heater
        logger('Humidificador no implementado');
        // ActivateDevice::dispatch(Activation::DEVICE_HUMIDIFIER, $activationCause, 0.5, $measure->id);
      }
    } else {
      // need to decrease humidity
      $activationCause = Activation::HIGH_HUMIDITY;
      if ($measure->outside_humidity < $deviation['obtained']) {
        // it's less humid outside. Use outside air humidity to decrease inside humidity

        logger('Activando ventilador para bajar la humedad utilizando el aire seco del exterior');
        ActivateDevice::dispatch(Activation::DEVICE_FAN, $activationCause, 0.5, $measure->id);

        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        ActivateDevice::dispatch(Activation::DEVICE_EXTRACTOR, $activationCause, 0.5, $measure->id);
      } else {
        // it's more humid than expected inside. Outside humidity won't help. Turn on extractor to remove humidity
        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        ActivateDevice::dispatch(Activation::DEVICE_EXTRACTOR, $activationCause, 0.5, $measure->id);
      }
    }
  }

  private function fixSoilHumidityDeviation(Measure $measure)
  {
    logger('Activando riego para proporcionar humedad al suelo');
    ActivateDevice::dispatch(Activation::DEVICE_WATER, Activation::LOW_SOIL_HUMIDITY, 0.5, $measure->id);
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
