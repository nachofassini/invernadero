<?php

namespace App\Observers;

use App\Models\Crop;
use App\Models\Measure;
use App\Models\Stage;
use Illuminate\Support\Facades\Date;

const ALERT_THRESHOLD = 0.1;
const DAYLIGHT_THRESHOLD = 50;

class MeasureObserver
{
    private function getCropDeviations(Stage $activeStage, Measure $lastMeasure)
    {
        // Calc temperature deviation
        /* $temperatureThreshold = ($activeStage->max_temperature - $activeStage->min_temperature) * ALERT_THRESHOLD;
        $maximumTemperatureLimit = $activeStage->max_temperature + $temperatureThreshold;
        $minimumTemperatureLimit = $activeStage->min_temperature - $temperatureThreshold;
        if ($lastMeasure->inside_temperature > $maximumTemperatureLimit) {
            dd('temp is higher than expected', "Expected: {$activeStage->max_temperature}", "Obtained: $lastMeasure->inside_temperature");
            // alert max temp surpassed
            // activate ventilation
        }
        if ($lastMeasure->inside_temperature < $minimumTemperatureLimit) {
            dd(['temp is lower than expected', "Expected: $activeStage->min_temperature", "Obtained: $lastMeasure->inside_temperature"]);
            // alert min temp surpassed
            // turn on heat
        } */

        // Calc humidity deviation
        /* $humidityThreshold = ($activeStage->max_humidity - $activeStage->min_humidity) * ALERT_THRESHOLD;
        $minimumHumidityLimit = max($activeStage->min_humidity - $humidityThreshold, 0);
        $maximumHumidityLimit = min($activeStage->max_humidity + $humidityThreshold, 100);
        if ($lastMeasure->inside_humidity > $maximumHumidityLimit) {
            dd('humidity is higher than expected', "Expected: {$activeStage->max_humidity}", "Obtained: $lastMeasure->inside_humidity");
            // alert max humidity surpassed
            // activate ventilation
        }
        if ($lastMeasure->inside_humidity < $minimumHumidityLimit) {
            dd(['humidity is lower than expected', "Expected: $activeStage->min_humidity", "Obtained: $lastMeasure->inside_humidity"]);
            // Alert min humidity surpassed
            //
        } */

        // Calc soil humidity deviation
        /* $soilHumidityThreshold = ($activeStage->max_soil_humidity - $activeStage->min_soil_humidity) * ALERT_THRESHOLD;
        $minimumSoilHumidityLimit = max($activeStage->min_soil_humidity - $soilHumidityThreshold, 0);
        $maximumSoilHumidityLimit = min($activeStage->max_soil_humidity + $soilHumidityThreshold, 100);
        if ($lastMeasure->soil_humidity > $maximumSoilHumidityLimit) {
            dd('soil humidity is higher than expected', "Expected: {$activeStage->max_soil_humidity}", "Obtained: $lastMeasure->soil_humidity");
            // alert max soil humidity surpassed
            // activate ventilation?
        }
        if ($lastMeasure->soil_humidity < $minimumSoilHumidityLimit) {
            dd(['soil humidity is lower than expected', "Expected: $activeStage->min_soil_humidity", "Obtained: $lastMeasure->inside_soil humidity"]);
            // Alert min soil humidity surpassed
            // irrigate?
        } */

        // Calc lightning deviation
        $lightingThreshold = $activeStage->light_hours * ALERT_THRESHOLD;
        //         SELECT COUNT(isDayLight)
        // FROM (SELECT IIF(inside_lighting > 50, 1,0) [isDayLight] FROM measures)
        // GROUP BY isDayLight
        $lastHoursLight = Measure::whereBetween('created_at', [Date::now(), Date::now()->subHours($activeStage->light_hours)])
            ->selectRaw('IIF(inside_lighting > 50, 1,0) AS isDayLight')
            ->get();
        $minimumSoilHumidityLimit = max($activeStage->min_soil_humidity - $lightingThreshold, 0);
        $maximumSoilHumidityLimit = min($activeStage->max_soil_humidity + $lightingThreshold, 100);
        if ($lastMeasure->soil_humidity > $maximumSoilHumidityLimit) {
            dd('soil humidity is higher than expected', "Expected: {$activeStage->max_soil_humidity}", "Obtained: $lastMeasure->soil_humidity");
            // alert max soil humidity surpassed
            // activate ventilation?
        }
        if ($lastMeasure->soil_humidity < $minimumSoilHumidityLimit) {
            dd(['soil humidity is lower than expected', "Expected: $activeStage->min_soil_humidity", "Obtained: $lastMeasure->inside_soil humidity"]);
            // Alert min soil humidity surpassed
            // irrigate?
        }
    }

    /**
     * Handle the Measure "created" event.
     *
     * @param  \App\Models\Measure  $measure
     * @return void
     */
    public function created(Measure $measure)
    {
        logger('Measure created', ['measure' => $measure]);

        $activeCrop = Crop::active()->first();

        if (!$activeCrop) {
            return 'No active crop';
        }

        $activeStage = $activeCrop->stages()
            ->where('from', '<=', Date::now())
            ->where('to', '>=', Date::now())
            ->first();

        if (!$activeStage) {
            return 'No active stage';
        }

        $deviations = $this->getCropDeviations($activeStage, $measure);

        dd('deviations', $deviations);
    }
}
