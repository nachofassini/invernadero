<?php

namespace App\Observers;

use App\Models\Crop;
use App\Models\Measure;

class MeasureObserver
{
    /**
     * Handle the Measure "created" event.
     *
     * @param  \App\Models\Measure  $measure
     * @return void
     */
    public function created(Measure $measure)
    {
        logger('Measure created', $measure->toArray());

        $activeCrop = Crop::active()->first();

        if (!$activeCrop || !$activeCrop->activeStage) {
            logger('No active crop / stage');
            return;
        }

        $activeCrop->handlePlanDeviations($measure);
    }
}
