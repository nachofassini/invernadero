<?php

namespace App\GraphQL\Queries;

final class ActivePlan
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            "activePlan" => [
                "crop" => [],
                "stage" => [],
            ]
        ];
    }
}
