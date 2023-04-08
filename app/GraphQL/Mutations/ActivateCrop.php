<?php

namespace App\GraphQL\Mutations;

use App\Models\Crop;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class ActivateCrop
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $crop = Crop::findOrFail($args["id"]);
        DB::transaction(function () use ($args, &$crop) {
            $crop->active_since = Carbon::now();
            $crop->save();

            Crop::where('id', '!=', $args["id"])->whereNotNull('active_since')->update(["active_since" => null]);
        });
        return $crop;
    }
}
