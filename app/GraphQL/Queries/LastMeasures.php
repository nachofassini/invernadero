<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

final class LastMeasures
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return  DB::table('measures')->latest()->limit($args["limit"])->offset($args["offset"])->get();
    }
}
