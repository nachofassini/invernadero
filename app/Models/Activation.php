<?php

namespace App\Models;

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
     * Get the measure that triggered this activation
     */
    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }
}
