<?php

namespace App\GraphQL\Mutations;

use App\Models\Activation;

final class ActivateDevice
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return Activation::create(["activated_by" => 'manual', "device" => $args['device'], "amount" => $args['amount']]);
    }
}
