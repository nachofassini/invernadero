<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'consumption' => 'double',
        'outside_temperature' => 'double',
        'outside_humidity' => 'double',
        'inside_temperature' => 'double',
        'inside_humidity' => 'double',
        'soil_humidity' => 'double',
        'co2' => 'double',
        'lighting' => 'double',
    ];

    /**
     * Scope a query to only include popular users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLast(Builder $query) {
        return $query->latest();
    }
}
