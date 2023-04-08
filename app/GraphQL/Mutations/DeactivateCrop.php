<?php

namespace App\GraphQL\Mutations;

use App\Models\Crop;

final class DeactivateCrop
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return Crop::whereNotNull('active_since')->update(["active_since" => null]);
    }
}
