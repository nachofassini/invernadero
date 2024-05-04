<?php

namespace App\GraphQL\Mutations;

use App\Models\Activation;

final class DeactivateDevice
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $activation = Activation::active()->whereDevice($args["device"])->get()->first();
        return $activation->deactivate(null);
    }
}
