<?php

namespace App\GraphQL\Mutations;

use App\Models\Activation;
use Illuminate\Support\Facades\Artisan;

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

        Artisan::queue('device:switch', [
            'device' => $deviceName, '--turn' => 'on', '--time' => $args['amount'], 'cause' => Activation::MANUAL
        ]);

        // Await queue to execute the command (it creates the activation record as soon it's executed)
        sleep(2);

        return Activation::latest()->first();
    }
}
