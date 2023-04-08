<?php

namespace App\GraphQL\Queries;

use App\Models\Activation;

final class Activations
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $deviceName = $args['device'] ?? null;
        return Activation::when($deviceName, function ($query, $deviceName) {
            $query->where('device', $deviceName);
        })->latest()->limit(5)->get();
    }
}
