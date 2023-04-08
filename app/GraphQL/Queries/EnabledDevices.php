<?php

namespace App\GraphQL\Queries;

use App\Models\Activation;

final class EnabledDevices
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return Activation::whereNull('active_until')->get();
    }
}
