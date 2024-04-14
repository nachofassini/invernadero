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
        $crop =  Crop::active()->first();

        if (!$crop) {
            return null;
        }

        $crop->deactivate();

        return $crop;
    }
}
