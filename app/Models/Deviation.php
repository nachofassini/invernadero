<?php

namespace App\Models;

use App\Models\Traits\FixPlanDeviations;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deviation extends Model
{
    use FixPlanDeviations, HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expected' => 'double',
        'observed' => 'double',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'expected',
        'observed',
        'detection_id',
        'fix_id',
    ];

    /**
     * @return Collection<Deviation>
     */
    public static function getActives(): Collection
    {
        return self::active()->with('activations')->get();
    }

    public static function getSunriseTime(): DateTime
    {
        $latitude = -24.7821; // Latitude for Salta, Argentina
        $longitude = -65.4232; // Longitude for Salta, Argentina

        $sun_info = date_sun_info(time(), $latitude, $longitude);

        $sunriseTime = new DateTime('@'.$sun_info['sunrise']);
        $sunriseTime->setTimezone(new DateTimeZone('America/Argentina/Salta'));

        return $sunriseTime;
    }

    /**
     * Determine if there's an active deviation for a given type
     */
    public static function isInProgress(string $type): bool
    {
        return Deviation::active()->whereType($type)->exists();
    }

    public function detection(): BelongsTo
    {
        return $this->belongsTo(Measure::class, 'detection_id');
    }

    public function fix(): BelongsTo
    {
        return $this->belongsTo(Measure::class, 'fix_id');
    }

    /**
     * Scope to get active deviations
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('fix_id');
    }

    public function deactivate(?Measure $measure = null): self
    {
        $this->fix_id = $measure->id;
        $this->save();
        $this->activations()->each->deactivate();

        return $this;
    }

    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }
}
