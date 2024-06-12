<?php

namespace App\Observers;

use App\Models\Activation;
use App\Models\Crop;

class CropObserver
{
    /**
     * Handle the Crop "deleting" event.
     */
    public function deleting(Crop $crop): void
    {
        $crop->deactivate();
    }

    /**
     * Handle the Crop "deleted" event.
     */
    public function deleted(Crop $crop): void
    {
        Activation::active()->get()->each->deactivate();
    }
}
