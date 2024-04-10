<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use App\Models\Crop;

class GreenhouseController extends Controller
{
    public function index()
    {
        $lastMeasure = Measure::latest()->first();

        $activeCrop = Crop::active()->first();

        if (!$activeCrop) {
            return 'No active crop';
        }

        $activeCrop->handlePlanDeviations($lastMeasure);

        return true;
    }
}
