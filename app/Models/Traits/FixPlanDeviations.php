<?php

namespace App\Models\Traits;

use App\Models\Activation;
use App\Models\Measure;

trait FixPlanDeviations
{
  public Measure $measure;

  /**
   * Activate a device to fix a deviation
   *
   * @param string $device
   * @param array $deviation
   * @return Activation
   */
  private function activateDevice(string $device, array $deviation): Activation
  {
    return Activation::create([
      'device' => $device,
      'activated_by' => $deviation['type'],
      'deviation' => ["obtained" => $deviation['obtained'], "expected" => $deviation['expected']],
      'measure_id' => $this->measure->id,
    ]);
  }

  private function fixLightDeviation($deviation)
  {
    logger('Activando LED para proporcionar luz artificial');
    $this->activateDevice(Activation::DEVICE_LIGHT, $deviation);
  }

  private function fixTemperatureDeviation($deviation)
  {
    if ($deviation['type'] === Activation::LOW_TEMPERATURE) {
      // need to heat up
      $requiredOutsideTemperature = $deviation['obtained'] + $deviation['obtained'] * TEMPERATURE_THRESHOLD;
      if ($this->measure->outside_temperature > $requiredOutsideTemperature) {
        // it's hotter outside. Use outside temperature air to heat up
        logger('Activando ventilador para calentar usando el aire exterior');
        $this->activateDevice(Activation::DEVICE_FAN, $deviation);
      } else {
        // it's colder outside than expected inside. Outside temp won't help. Turn on heater
        logger('Calentar: Calefacción no implementada');
      }
    } else {
      // need to cool down
      $requiredOutsideTemperature = $deviation['obtained'] - $deviation['obtained'] * TEMPERATURE_THRESHOLD;
      if ($this->measure->outside_temperature < $requiredOutsideTemperature) {
        // it's colder outside. Use outside air temperature to cool down inside
        logger('Activando ventilador para enfriar con el aire fresco del exteriór');
        $this->activateDevice(Activation::DEVICE_FAN, $deviation);
      } else {
        // it's hotter than expected inside. Outside temp won't help. Turn on extractor to remove heat
        logger('Activando extractor para remover el aire caliente');
        $this->activateDevice(Activation::DEVICE_EXTRACTOR, $deviation);
      }
    }
  }

  private function fixHumidityDeviation($deviation)
  {
    if ($deviation['type'] === Activation::LOW_HUMIDITY) {
      // need to increase humidity
      $requiredOutsideHumidity = $deviation['obtained'] + $deviation['obtained'] * HUMIDITY_THRESHOLD;
      if ($this->measure->outside_humidity > $requiredOutsideHumidity) {
        // it's more humid outside. Use outside air humidity to increase inside humidity
        logger('Activando ventilador para utilizar el aire humedo del exterior');
        $this->activateDevice(Activation::DEVICE_FAN, $deviation);
      } else {
        // it's more hum than expected inside. Outside temp won't help. Turn on heater
        logger('Humidificador no implementado');
      }
    } else {
      // need to decrease humidity
      $requiredOutsideHumidity = $deviation['obtained'] - $deviation['obtained'] * HUMIDITY_THRESHOLD;
      if ($this->measure->outside_humidity < $requiredOutsideHumidity) {
        // it's less humid outside. Use outside air humidity to decrease inside humidity
        logger('Activando ventilador para bajar la humedad utilizando el aire seco del exterior');
        $this->activateDevice(Activation::DEVICE_FAN, $deviation);

        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        $this->activateDevice(Activation::DEVICE_EXTRACTOR, $deviation);
      } else {
        // it's more humid than expected inside. Outside humidity won't help. Turn on extractor to remove humidity
        logger('Activando extractor para bajar la humedad removiendo aire humedo del interior');
        $this->activateDevice(Activation::DEVICE_EXTRACTOR, $deviation);
      }
    }
  }

  private function fixSoilHumidityDeviation($deviation)
  {
    logger('Activando riego para proporcionar humedad al suelo');
    $this->activateDevice(Activation::DEVICE_WATER, $deviation);
  }

  private function fixDeviations($deviations, Measure $measure)
  {
    $this->measure = $measure;

    foreach ($deviations as $deviation) {
      // Handle lighting
      if ($deviation['type'] == Activation::LOW_LIGHTING) {
        $this->fixLightDeviation($deviation);
      }

      // Handle temperature
      if ($deviation['type'] == Activation::LOW_TEMPERATURE || $deviation['type'] == Activation::HIGH_TEMPERATURE) {
        $this->fixTemperatureDeviation($deviation);
      }

      // Handle humidity (need to check if wont screw temperature when trying to fix humidity)
      if ($deviation['type'] == Activation::LOW_HUMIDITY || $deviation['type'] == Activation::HIGH_HUMIDITY) {
        $this->fixHumidityDeviation($deviation);
      }

      // Handle soil humidity
      if ($deviation['type'] == Activation::LOW_SOIL_HUMIDITY) {
        $this->fixSoilHumidityDeviation($deviation);
      }
    }
  }
}
