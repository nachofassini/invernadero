<?php

namespace App\Models;

use App\Models\Traits\CalculatesPlanDeviations;
use App\Models\Traits\FixPlanDeviations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use CalculatesPlanDeviations, FixPlanDeviations, HasFactory;

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
     * Deactivates current crop
     */
    public function deactivate()
    {
        $this->active_since = null;
        $this->save();
        return $this;
    }

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
            if ($daysActive >= $rangeStart && $daysActive < ($rangeStart + $stage->days)) {
                return $stage;
            }
            $rangeStart += $stage->days;
        }

        return null;
    }

    /**
     * @param  Measure $measure
     * Get plan deviations for current measure and trigger adjustments.
     */
    public function handlePlanDeviations(Measure $measure)
    {
        $deviations = $this->getPlanDeviations($measure);

        logger('Plan deviations', $deviations);

        $this->fixDeviations($deviations, $measure);
    }

    /**
     * Scope a query to only include active crops
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
