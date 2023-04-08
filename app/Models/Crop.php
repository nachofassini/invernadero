<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'active_since' => 'datetime',
    ];

    /**
     * Retrieves current crop status
     */
    public function getActiveAttribute()
    {
        return $this->active_since != null;
    }

    /**
     * Retrieves active crop cultivation end date
     */
    public function getActiveUntilAttribute()
    {
        if (!$this->active) {
            return null;
        }

        return $this->active_since->addDays($this->days);
    }

    /**
     * Retrieves days since it has been activated
     */
    public function getDayAttribute()
    {
        if (!$this->active) {
            return 0;
        }
        return Carbon::now()->diff($this->active_since)->days + 1;
    }

    /**
     * Retrieves crop total days
     */
    public function getDaysAttribute()
    {
        return $this->stages()->sum('days');
    }

    /**
     * Retrieves current active stage
     */
    public function getActiveStageAttribute()
    {
        if (!$this->active) {
            return null;
        }

        $daysActive = $this->day;

        $rangeStart = 0;
        foreach ($this->stages as $stage) {
            // TODO use stage init and end date instead of day number (to be more accurate)
            if ($daysActive >= $rangeStart && $daysActive < ($rangeStart + $stage->days)) {
                return $stage;
            }
            $rangeStart += $stage->days;
        }

        return null;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereNotNull('active_since');
    }

    /**
     * Get the stages.
     */
    public function stages()
    {
        return $this->hasMany(Stage::class)->orderBy('order');
    }

    /**
     * Get the stages.
     */
    public function stagesCount()
    {
        return $this->stagesCount();
    }
}
