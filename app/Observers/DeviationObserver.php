<?php

namespace App\Observers;

use App\Models\Deviation;

class DeviationObserver
{
    /**
     * Handle the Deviation "created" event.
     */
    public function created(Deviation $deviation): void
    {
        $deviation->handle($deviation);
    }
}
