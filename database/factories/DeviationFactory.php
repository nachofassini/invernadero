<?php

namespace Database\Factories;

use App\Enums\DeviationTypes;
use App\Models\Deviation;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deviation>
 */
class DeviationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(DeviationTypes::values()),
            'expected' => fake()->randomFloat(0, 30, 70),
            'observed' => function (array $attributes) {
                return str_starts_with($attributes['type'], 'low') ? fake()->randomFloat(1, 0, $attributes['expected']) : fake()->randomFloat(1, $attributes['expected'], 100);
            },
            'detection_id' => Measure::factory(),
            'fix_id' => Measure::factory(),
        ];
    }
}
