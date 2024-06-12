<?php

namespace App\Observers;

use App\Console\Commands\SwitchDevice;
use App\Jobs\DeactivateDevice;
use App\Models\Activation;
use Illuminate\Support\Facades\Artisan;

class ActivationObserver
{
    /**
     * Handle the Activation "creating" event.
     */
    public function creating(Activation $activation): array|bool
    {
        // If there's an active record for the same device, don't activate the device
        if (Activation::active()->whereDevice($activation->device)->get()->count()) {
            return false;
        }

        /*if (! $activation->activated_by) {
            $activation->activated_by = self::MANUAL;
        }*/

        return $activation;
    }

    /**
     * Handle the Activation "created" event.
     */
    public function created(Activation $activation): void
    {
        Artisan::queue(SwitchDevice::class, ['device' => $activation->device, '--turn' => 'on']);

        // Only manual activations will set a timeout to deactivate the device
        if ($activation->amount) {
            DeactivateDevice::dispatch($activation)->delay(now()->addSeconds($activation->amount * 60));
        }
    }
}
