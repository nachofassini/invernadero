<?php

namespace App\GraphQL\Queries;

use App\Models\Measure;
use Illuminate\Support\Facades\DB;

final class MeasuresAverageByHour
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $from = ($args['created_at']['from'])->toDateTimeString();
        $to = ($args['created_at']['to'])->toDateTimeString();

        return Measure::whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('AVG(consumption) as consumption'),
                DB::raw('AVG(inside_temperature) as inside_temperature'),
                DB::raw('AVG(outside_temperature) as outside_temperature'),
                DB::raw('AVG(inside_humidity) as inside_humidity'),
                DB::raw('AVG(outside_humidity) as outside_humidity'),
                DB::raw('AVG(soil_humidity) as soil_humidity'),
                DB::raw('AVG(co2) as co2'),
                DB::raw('AVG(lighting) as lighting'),
            )
            ->groupByRaw('strftime("%H", created_at)')
            ->having('id', '>', 0)
            ->get();
    }
}
