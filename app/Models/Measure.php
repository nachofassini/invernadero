<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at',
        'updated_at',
        'consumption',
        'inside_temperature',
        'outside_temperature',
        'inside_humidity',
        'outside_humidity',
        'soil_humidity',
        'co2',
        'lighting',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'consumption' => 'double',
        'inside_temperature' => 'double',
        'outside_temperature' => 'double',
        'inside_humidity' => 'double',
        'outside_humidity' => 'double',
        'soil_humidity' => 'double',
        'co2' => 'integer',
        'lighting' => 'double',
    ];

    /**
     * Scope to get latests
     *
     * @return Builder
     */
    public function scopeLast(Builder $query)
    {
        return $query->latest();
    }

    /**
     * Get the activations triggered by this measure.
     */
    public function deviations(): HasMany
    {
        return $this->hasMany(Deviation::class, 'detection_id');
    }

    /**
     * Get the activations fixed by this measure.
     */
    public function fixes(): HasMany
    {
        return $this->hasMany(Deviation::class, 'fix_id');
    }
}
