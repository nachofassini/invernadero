<?php

namespace App\Models;

use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    use HasFactory;

    const LOW_TEMPERATURE = 'low_temperature';
    const HIGH_TEMPERATURE = 'high_temperature';
    const LOW_HUMIDITY = 'low_humidity';
    const HIGH_HUMIDITY = 'high_humidity';
    const LOW_SOIL_HUMIDITY = 'low_soil_humidity';
    const HIGH_SOIL_HUMIDITY = 'high_soil_humidity';
    const LOW_CO2 = 'low_co2';
    const HIGH_CO2 = 'high_co2';
    const LOW_LIGHTING = 'low_lighting';
    const MANUAL = 'manual';

    const DEVICE_FAN = 'fan';
    const DEVICE_EXTRACTOR = 'extractor';
    const DEVICE_LIGHT = 'light';
    const DEVICE_WATER = 'irrigation';

    const DEVICES = [
        self::DEVICE_FAN,
        self::DEVICE_EXTRACTOR,
        self::DEVICE_LIGHT,
        self::DEVICE_WATER,
    ];

    const DEVICE_PINS = [
        self::DEVICE_FAN => 6,
        self::DEVICE_EXTRACTOR => 13,
        self::DEVICE_LIGHT => 19,
        self::DEVICE_WATER => 26,
    ];

    const UNIT_MILLIMETERS = 'mm3';
    const UNIT_CUBIC_METERS = 'm3';
    const UNIT_PERCENTAGE = '%';
    const UNIT_HOURS = 'Hs.';
    const UNIT_MINUTES = 'Mins.';
    const UNIT_PARTS_PER_MILLION = 'ppm';
    const UNIT_CELSIUS = 'ºC';

    const MEASURE_UNITS = [
        self::UNIT_MILLIMETERS,
        self::UNIT_CUBIC_METERS,
        self::UNIT_PERCENTAGE,
        self::UNIT_HOURS,
        self::UNIT_MINUTES,
        self::UNIT_PARTS_PER_MILLION,
        self::UNIT_CELSIUS,
    ];

    const TYPES = [
        self::LOW_TEMPERATURE,
        self::HIGH_TEMPERATURE,
        self::LOW_HUMIDITY,
        self::HIGH_HUMIDITY,
        self::LOW_SOIL_HUMIDITY,
        self::HIGH_SOIL_HUMIDITY,
        self::LOW_CO2,
        self::HIGH_CO2,
        self::LOW_LIGHTING,
        self::MANUAL,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active_until' => 'datetime',
        'device' => 'string',
        'activated_by' => 'string',
        'measure_id' => 'integer',
        'amount' => 'double',
        'measure_unit' => 'string',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'activated_by',
        'measure_id',
        'device',
        'amount',
        'measure_unit',
    ];

    public function getEnabledAttribute()
    {
        return $this->active_until === null;
    }

    public static function getSunriseTime()
    {
        $latitude = -24.7821; // Latitude for Salta, Argentina
        $longitude = -65.4232; // Longitude for Salta, Argentina

        $sun_info = date_sun_info(time(), $latitude, $longitude);

        $sunriseTime = new DateTime('@' . $sun_info['sunrise']);
        $sunriseTime->setTimezone(new DateTimeZone('America/Argentina/Salta'));

        return $sunriseTime;
    }

    /**
     * Scope to get latests
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereNull('active_until');
    }

    /**
     * Scope to get latests
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLast(Builder $query)
    {
        return $query->latest();
    }

    /**
     * Scope to filter bt device
     *
     * @param  Builder  $query
     * @param  array{}  $args
     * @return Builder
     */
    public function scopeFilterByDevice(Builder $query, array $args = [])
    {
        $deviceName = $args['device'] ?? null;
        return $query->when($deviceName, function ($query, $deviceName) {
            $query->where('device', $deviceName);
        });
    }

    /**
     * Get the measure that triggered this activation
     */
    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }
}
