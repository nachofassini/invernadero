<?php

namespace App\Models\Traits;

use App\Enums\DeviationTypes;
use App\Enums\Devices;
use App\Models\Activation;
use App\Models\Deviation;

trait FixPlanDeviations
{
    protected function handle(Deviation $deviation): void
    {
        // Handle lighting
        if ($this->type == DeviationTypes::LOW_LIGHTING->value) {
            $this->fixLightDeviation($deviation);
        }

        // Handle temperature
        if ($deviation->type == DeviationTypes::LOW_TEMPERATURE->value || $deviation->type == DeviationTypes::HIGH_TEMPERATURE->value) {
            $this->fixTemperatureDeviation($deviation);
        }

        // Handle humidity (need to check if won`t screw temperature when trying to fix humidity)
        if ($deviation->type == DeviationTypes::LOW_HUMIDITY->value || $deviation->type == DeviationTypes::HIGH_HUMIDITY->value) {
            $this->fixHumidityDeviation($deviation);
        }

        // Handle soil humidity
        if ($deviation->type == DeviationTypes::LOW_SOIL_HUMIDITY->value) {
            $this->fixSoilHumidityDeviation($deviation);
        }
    }

    /**
     * Fix lighting deviation
     */
    private function fixLightDeviation($deviation): void
    {
        $this->activateDevice(Devices::LIGHT->value, $deviation);
    }

    /**
     * Activate a device to fix a deviation
     */
    private function activateDevice(string $device, Deviation $deviation): Activation
    {
        return Activation::create(['device' => $device, 'deviation_id' => $deviation->id]);
    }

    /**
     * Fix temperature deviation
     */
    private function fixTemperatureDeviation(Deviation $deviation): void
    {
        if ($deviation->type === DeviationTypes::LOW_TEMPERATURE->value) {
            // need to heat up
            $requiredOutsideTemperature = $deviation->obtained + $deviation->obtained * TEMPERATURE_THRESHOLD;
            if ($this->measure->outside_temperature > $requiredOutsideTemperature) {
                // it's hotter outside. Use outside temperature air to heat up
                $this->activateDevice(Devices::FAN->value, $deviation);
            } else {
                // it's colder outside than expected inside. Outside temp won't help. Turn on heater
                logger('Calentar: Calefacción no implementada');
            }
        } else {
            // need to cool down
            $requiredOutsideTemperature = $deviation->obtained - $deviation->obtained * TEMPERATURE_THRESHOLD;
            if ($this->measure->outside_temperature < $requiredOutsideTemperature) {
                // it's colder outside. Use outside air temperature to cool down inside
                $this->activateDevice(Devices::FAN->value, $deviation);
            } else {
                // it's hotter than expected inside. Outside temp won't help. Turn on extractor to remove heat
                $this->activateDevice(Devices::EXTRACTOR->value, $deviation);
            }
        }
    }

    /**
     * Fix humidity deviation
     */
    private function fixHumidityDeviation(Deviation $deviation): void
    {
        if ($deviation->type === DeviationTypes::LOW_HUMIDITY->value) {
            // need to increase humidity
            $requiredOutsideHumidity = $deviation->obtained + $deviation->obtained * HUMIDITY_THRESHOLD;
            if ($this->measure->outside_humidity > $requiredOutsideHumidity) {
                // it's more humid outside. Use outside air humidity to increase inside humidity
                $this->activateDevice(Devices::FAN->value, $deviation);
            } else {
                // it's more hum than expected inside. Outside temp won't help. Turn on heater
                logger('Humidificador no implementado');
            }
        } else {
            // need to decrease humidity
            $requiredOutsideHumidity = $deviation->obtained - $deviation->obtained * HUMIDITY_THRESHOLD;
            if ($this->measure->outside_humidity < $requiredOutsideHumidity) {
                // it's less humid outside. Use outside air humidity to decrease inside humidity
                $this->activateDevice(Devices::FAN->value, $deviation);

                $this->activateDevice(Devices::EXTRACTOR->value, $deviation);
            } else {
                // it's more humid than expected inside. Outside humidity won't help. Turn on extractor to remove humidity
                $this->activateDevice(Devices::EXTRACTOR->value, $deviation);
            }
        }
    }

    /**
     * Fix soil humidity deviation
     */
    private function fixSoilHumidityDeviation(Deviation $deviation): void
    {
        $this->activateDevice(Devices::WATER_PUMP->value, $deviation);
    }
}
