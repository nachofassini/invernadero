<?php

namespace Database\Factories;

use App\Models\Activation;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activation>
 */
class ActivationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'active_until' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], Carbon::parse($attributes['created_at'])->addHour());
            },    
            'device' => fake()->randomElement(['fan', 'extractor', 'light', 'irrigation']),
            'activated_by' => fake()->randomElement(['low_temp', 'high_temp', 'low_humidity', 'high_humidity', 'low_soil_humidity', 'high_soil_humidity', 'low_lighting', 'low_co2', 'high_co2', 'manual']),
            'measure_id' => Measure::factory(),
            'amount' => fake()->randomFloat(1, 0, 1200),
            'measure_unit' => fake()->randomElement(['mm3', 'm3', '%', 'Hs.', 'Mins.', 'ppm', 'ºC']),
        ];
    }
}
