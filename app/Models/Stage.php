<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'order' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'order' => 'integer',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Set order
        static::creating(function ($attr) {
            $cropStages = self::where('crop_id', $attr['crop_id'])->count();
            $attr['order'] = $cropStages + 1;
            return $attr;
        });
    }

    /**
     * Determines if stage is active or not
     */
    public function getActiveAttribute()
    {
        return $this->crop->active && $this->crop->activeStage->id === $this->id;
    }

    /**
     * Retrieves days since it has been activated
     */
    public function getDayAttribute()
    {
        if (!$this->active) {
            return 0;
        }

        $cropDaysActive = $this->crop->day;

        $rangeStart = 0;
        foreach ($this->crop->stages as $stage) {
            // TODO use stage init and end date instead of day number (to be more accurate)
            if ($cropDaysActive >= $rangeStart && $cropDaysActive < ($rangeStart + $stage->days)) {
                return $cropDaysActive - $rangeStart + 1;
            }
            $rangeStart += $stage->days;
        }

        return 0;
    }

    /**
     * Get the crop
     */
    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
