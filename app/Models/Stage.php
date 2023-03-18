<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'days' => 'integer',
        'min_temperature' => 'double',
        'max_temperature' => 'double',
        'min_humidity' => 'double',
        'max_humidity' => 'double',
        'min_co2' => 'double',
        'max_co2' => 'double',
        'irrigation' => 'integer',
        'light_hours' => 'double',
    ];

    /**
     * Get the crop
     */
    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
