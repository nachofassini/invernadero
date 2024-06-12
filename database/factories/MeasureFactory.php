<?php

namespace Database\Factories;

use App\Models\Measure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Measure>
 */
class MeasureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Measure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consumption' => fake()->randomFloat(1, 0, 12),
            'inside_temperature' => fake()->randomFloat(1, 0, 45),
            'outside_temperature' => fake()->randomFloat(1, -10, 45),
            'inside_humidity' => fake()->randomFloat(1, 0, 100),
            'outside_humidity' => fake()->randomFloat(1, 0, 100),
            'soil_humidity' => fake()->randomFloat(1, 0, 100),
            'co2' => fake()->numberBetween(400, 1200),
            'lighting' => fake()->randomFloat(1, 0, 100),
        ];
    }
}
