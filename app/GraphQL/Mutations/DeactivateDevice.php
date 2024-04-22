<?php

namespace App\GraphQL\Mutations;

use App\Jobs\DeactivateDevice as DeactivateDeviceJob;
use App\Models\Activation;

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

        $activation = Activation::where('device', $args["device"])->active()->first();

        if (!$activation) {
            return null;
        }

        DeactivateDeviceJob::dispatchSync($activation);

        return $activation->fresh();
    }
}
