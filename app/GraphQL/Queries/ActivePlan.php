<?php

namespace App\GraphQL\Queries;

use App\Models\Crop;

final class ActivePlan
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $activeCrop = Crop::whereNotNull('active_since')->first();
        return [
            "crop" => $activeCrop,
            "stage" => $activeCrop->activeStage,
        ];
    }
}
