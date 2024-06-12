<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deviation extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expected' => 'double',
        'observed' => 'double',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'expected',
        'observed',
        'detection_id',
        'fix_id',
    ];

    public static function getSunriseTime(): \DateTime
    {
        $latitude = -24.7821; // Latitude for Salta, Argentina
        $longitude = -65.4232; // Longitude for Salta, Argentina

        $sun_info = date_sun_info(time(), $latitude, $longitude);

        $sunriseTime = new DateTime('@'.$sun_info['sunrise']);
        $sunriseTime->setTimezone(new DateTimeZone('America/Argentina/Salta'));

        return $sunriseTime;
    }

    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }

    public function detection(): BelongsTo
    {
        return $this->belongsTo(Measure::class, 'detection_id');
    }

    public function fix(): BelongsTo
    {
        return $this->belongsTo(Measure::class, 'fix_id');
    }
}
