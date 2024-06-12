<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Measure;

class GreenhouseController extends Controller
{
    public function index()
    {
        // return true;
        $measure = null;
        // $measure = Measure::latest()->first();
        Measure::unguarded(function () use (&$measure) {
            $measure = Measure::create([
                'inside_temperature' => 20,
                'outside_temperature' => 25,
                'lighting' => 55,
                'inside_humidity' => 78,
                'outside_humidity' => 80,
                'soil_humidity' => 32,
            ]);
        });

        $activeCrop = Crop::getActive();

        if (! $activeCrop->activeStage) {
            return 'No active crop';
        }

        dd($activeCrop->activeStage);

        $activeCrop->handlePlanDeviations($measure);

        return true;
    }
}
