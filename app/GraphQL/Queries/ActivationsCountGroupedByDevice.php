<?php

namespace App\GraphQL\Queries;

use App\Models\Activation;

final class ActivationsCountGroupedByDevice
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $lastActivations = Activation::latest()->select('id')->limit($args["amount"])->get();

        return Activation::latest()
            ->selectRaw('count(device) as count, device')
            ->whereIn('id', $lastActivations)
            ->groupBy('device')
            ->get();
    }
}
