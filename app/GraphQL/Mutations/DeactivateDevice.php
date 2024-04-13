<?php

namespace App\GraphQL\Mutations;

use App\Models\Activation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

final class DeactivateDevice
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

        Artisan::queue($command, ['--turn' => 'off', 'cause' => Activation::MANUAL]);

        // Await queue to execute the command (it creates the activation record as soon it's executed)
        sleep(2);

        return Activation::where('device', $args["device"])->whereNull('active_until')->update(["active_until" => Carbon::now()]);
    }
}
