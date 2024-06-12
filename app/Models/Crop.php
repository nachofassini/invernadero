<?php

namespace App\Models;

use App\Models\Traits\CalculatesPlanDeviations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use CalculatesPlanDeviations, HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'active_since' => 'datetime',
        'active_until' => 'datetime',
        'days' => 'int',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['active_until', 'days'];

    public static function getActive(): ?Crop
    {
        return self::active()->first();
    }

    /**
     * Retrieves current crop status
     */
    public function getActiveAttribute(): bool
    {
        return $this->active_since && $this->active_since->isPast() && $this->active_until->isFuture();
    }

    /**
     * Retrieves days since it has been activated
     */
    public function getDayAttribute(): int
    {
        if (! $this->active) {
            return 0;
        }

        return Carbon::now()->diff($this->active_since)->days;
    }

    /**
     * Retrieves current active stage
     */
    public function getActiveStageAttribute()
    {
        if (! $this->active) {
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
     */
    public function getStageRangesAttribute(): ?array
    {
        if (! $this->active) {
            return null;
        }

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
     * Get plan deviations for current measure and trigger adjustments.
     */
    public function handlePlanDeviations(Measure $measure): void
    {
        $deviationsBeingHandled = Deviation::getActives();

        $detectedDeviations = collect($this->getPlanDeviations($measure));

        $deviationsBeingHandled->filter(function ($activeCorrection) use ($detectedDeviations) {
            return ! $detectedDeviations->pluck('type')->contains($activeCorrection->type);
        })->each->deactivate($measure);
    }

    /**
     * Deactivates current crop
     */
    public function deactivate(): self
    {
        $this->active_since = null;
        $this->save();

        return $this;
    }

    /**
     * Scope a query to only include active crops
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNotNull('active_since');
    }

    /**
     * Get the stages.
     */
    public function stagesCount()
    {
        return $this->stagesCount();
    }

    /**
     * Retrieves crop total days
     */
    public function days(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->stages()->sum('days'),
        );
    }

    /**
     * Get the stages.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class)->orderBy('order');
    }

    /**
     * Retrieves active crop cultivation end date
     */
    protected function activeUntil(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['active_since']
                ? Carbon::parse($attributes['active_since'])->addDays($this->days) : null
        );
    }
}
