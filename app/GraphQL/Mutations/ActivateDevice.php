<?php

namespace App\GraphQL\Mutations;

use App\Jobs\ActivateDevice as ActivateDeviceJob;
use App\Models\Activation;

final class ActivateDevice
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        logger('ActivateDevice', $args);
        $deviceName = $args['device'];
        if (!in_array($deviceName, Activation::DEVICES)) {
            return null;
        }

        $isDeviceActive = Activation::active()->where('device', $deviceName)->count() > 0;

        if ($isDeviceActive) {
            return null;
        }

        ActivateDeviceJob::dispatchSync($deviceName, Activation::MANUAL, $args['amount']);

        // Await queue to execute the command (it creates the activation record as soon it's executed)
        // sleep(3);

        return Activation::latest()->first();
    }
}
