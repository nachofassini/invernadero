<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    use HasFactory;

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
        'device',
        'amount',
        'activated_by',
    ];

    public function getEnabledAttribute()
    {
        return $this->active_until === null;
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
