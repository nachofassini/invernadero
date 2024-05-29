<?php

namespace App\Models;

use App\Models\Traits\CalculatesPlanDeviations;
use App\Models\Traits\FixPlanDeviations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use CalculatesPlanDeviations, FixPlanDeviations, HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'active_since' => 'datetime',
        'active_until' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Deactivate crop when it is deleted
        static::deleting(function (Crop $crop) {
            $crop->deactivate();
        });

        // Deactivate all weather corrections when crop is deleted
        static::deleting(function () {
            Activation::active()->get()->each->deactivate();
        });
    }

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
        return $this->active_since && $this->active_since->isPast() && $this->active_until->isFuture();
    }

    /**
     * Retrieves active crop cultivation end date
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function activeUntil(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['active_since'] ? (new Carbon($attributes['active_since']))->addDays($this->days) : null,
        );
    }

    /**
     * Retrieves days since it has been activated
     */
    public function getDayAttribute()
    {
        if (!$this->active) return 0;
        return Carbon::now()->diff($this->active_since)->days;
    }

    /**
     * Retrieves crop total days
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function days(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stages()->sum('days'),
        );
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
     * Retrieves the date where the stage will became active
     * @return []
     */
    public function getStageRangesAttribute()
    {
        if (!$this->active) return null;

        $ranges = [];
        $rangeStart = $this->active_since;
        foreach ($this->stages as $stage) {
            $rangeEnd = $rangeStart->copy()->addDays($stage->days);

            $ranges[$stage->id] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'from' => $rangeStart,
                'to' => $rangeEnd,
            ];

            $rangeStart = $rangeEnd;
        }

        return $ranges;
    }

    /**
     * @param  Measure $measure
     * Get plan deviations for current measure and trigger adjustments.
     */
    public function handlePlanDeviations(Measure $measure)
    {
        $activeCorrections = Activation::active()->get();
        logger('Active corrections', $activeCorrections->toArray());

        $detectedDeviations = collect($this->getPlanDeviations($measure));
        logger('Plan deviations', $detectedDeviations->toArray());

        $deviationsToFix = $detectedDeviations->reject(function ($deviation) use ($activeCorrections) {
            return $activeCorrections->pluck('activated_by')->contains($deviation['type']);
        });
        $deviationsFixed = $activeCorrections->filter(function ($activeCorrection) use ($detectedDeviations) {
            return !$detectedDeviations->pluck('type')->contains($activeCorrection->activated_by);
        });
        $deviationsInProgress = $activeCorrections->except($deviationsFixed->pluck('id')->toArray());

        logger('Deviations to fix', $deviationsToFix->toArray());
        $this->fixDeviations($deviationsToFix, $measure);

        logger('Corrections to deactivate', $deviationsFixed->toArray());
        $deviationsFixed->each(fn ($activeCorrection) => $activeCorrection->deactivate($measure));

        logger('Corrections still in progress', $deviationsInProgress->toArray());
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
