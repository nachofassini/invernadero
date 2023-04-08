<?php

namespace App\GraphQL\Mutations;

use App\Models\Activation;
use Carbon\Carbon;

final class DeactivateDevice
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return Activation::where('device', $args["device"])->whereNull('active_until')->update(["active_until" => Carbon::now()]);
    }
}
