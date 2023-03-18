<?php

namespace App\Models;

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
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only include popular users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the stages.
     */
    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * Get the stages.
     */
    public function stagesCount()
    {
        return $this->hasMany(Stage::class)->stagesCount();
    }
}
