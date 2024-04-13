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
        $deviceName = $args['device'];
        if (!in_array($deviceName, Activation::DEVICES)) {
            return null;
        }

        Artisan::queue('device:switch', [
            'device' => $deviceName, '--turn' => 'off', 'cause' => Activation::MANUAL
        ]);

        // Await queue to execute the command (it creates the activation record as soon it's executed)
        sleep(2);

        return Activation::where('device', $args["device"])->whereNull('active_until')->update(["active_until" => Carbon::now()]);
    }
}
