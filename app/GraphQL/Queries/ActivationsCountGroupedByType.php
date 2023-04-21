<?php

namespace App\GraphQL\Queries;

use App\Models\Activation;

final class ActivationsCountGroupedByType
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $lastActivations = Activation::latest()->select('id')->limit($args["amount"]);

        return Activation::latest()
            ->selectRaw('count(activated_by) as count, activated_by')
            ->whereIn('id', $lastActivations)
            ->groupBy('activated_by')
            ->get();
    }
}
