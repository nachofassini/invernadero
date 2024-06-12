<?php

namespace App\Models;

use App\Console\Commands\SwitchDevice;
use App\Enums\Devices;
use App\Enums\MeasureUnits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class Activation extends Model
{
    use HasFactory;

    const MANUAL = 'manual';

    const DEVICE_PINS = [
        Devices::FAN->value => 6,
        Devices::EXTRACTOR->value => 13,
        Devices::LIGHT->value => 19,
        Devices::WATER_PUMP->value => 26,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active_until' => 'datetime',
        'deviation_id' => 'integer',
        'device' => 'string',
        'amount' => 'double',
        'measure_unit' => 'string',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'active_until',
        'deviation_id',
        'device',
        'amount',
        'measure_unit',
    ];

    /**
     *  Get active devices.
     *
     * @return Collection Activation
     */
    public static function getActives(): Collection
    {
        return self::active()->get();
    }

    public function deactivate(): self
    {
        // Queued deactivations might try to deactivate a device that was manually deactivated
        if (! $this->enabled) {
            return $this;
        }

        Artisan::queue(SwitchDevice::class, ['device' => $this->device, '--turn' => 'off']);

        $this->active_until = now();
        $interval = $this->created_at->diff($this->active_until);
        $this->amount = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i + $interval->s / 60;
        $this->measure_unit = MeasureUnits::MINUTES->value;
        $this->save();

        return $this;
    }

    /**
     * Scope to get latests actives
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('active_until');
    }

    /**
     * Scope to get latests
     */
    public function scopeLast(Builder $query): void
    {
        $query->latest();
    }

    /**
     * Scope to filter bt device
     *
     * @param  array{}  $args
     */
    public function scopeFilterByDevice(Builder $query, array $args = []): void
    {
        $deviceName = $args['device'] ?? null;

        $query->when($deviceName, function ($query, $deviceName) {
            $query->where('device', $deviceName);
        });
    }

    /**
     * Get the deviation
     *
     * @return HasOneThrough Measure
     */
    public function detection(): HasOneThrough
    {
        return $this->hasOneThrough(Measure::class, Deviation::class, 'id', 'id', 'deviation_id', 'detection_id');
    }

    /**
     * Get the measure that triggered this activation
     *
     * @return HasOne Deviation
     */
    public function deviation(): HasOne
    {
        return $this->hasOne(Deviation::class);
    }

    /**
     * Get the measure that fixed this activation
     *
     * @return HasOneThrough Measure
     */
    public function fix(): HasOneThrough
    {
        return $this->hasOneThrough(Measure::class, Deviation::class, 'id', 'id', 'fix_id', 'detection_id');
    }

    /**
     * Get device enabled status
     */
    protected function enabled(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['active_until'] === null,
        );
    }
}
