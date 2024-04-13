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
        switch ($args['device']) {
            case Activation::DEVICE_FAN:
                $command = 'fan:switch';
                break;
            case Activation::DEVICE_EXTRACTOR:
                $command = 'extractor:switch';
                break;
            case Activation::DEVICE_WATER:
                $command = 'water:switch';
                break;
            case Activation::DEVICE_LIGHT:
                $command = 'led:switch';
                break;
        }

        if (!isset($command)) {
            return null;
        }

        $isDeviceActive = Activation::active()->where('device', $args['device'])->count() > 0;

        if ($isDeviceActive) {
            return null;
        }

        Artisan::queue($command, ['--turn' => 'on', '--time' => $args['amount'], 'cause' => Activation::MANUAL]);

        // Await queue to execute the command (it creates the activation record as soon it's executed)
        sleep(2);

        return Activation::latest()->first();
    }
}
