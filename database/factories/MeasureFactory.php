<?php

namespace Database\Factories;

use App\Models\Measure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measure>
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
    public function definition()
    {
        return [
            'consumption' => fake()->randomFloat(2, 0, 12), // consumo
            'outside_temperature' => fake()->randomFloat(2, -10, 45), // Temperatura externa
            'outside_humidity' => fake()->randomFloat(2, 0, 100), // Humedad interna
            'inside_temperature' => fake()->randomFloat(2, 0, 45), // Temperatura interna
            'inside_humidity' => fake()->randomFloat(2, 0, 100), // Humedad interna
            'soil_humidity' => fake()->randomFloat(2, 0, 100), // Humedad del suelo
            'co2' => fake()->randomFloat(2, 0, 100), // Concentración CO2
            'lighting' => fake()->randomFloat(2, 0, 100),  // Iluminación
        ];
    }
}
