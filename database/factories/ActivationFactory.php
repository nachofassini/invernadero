<?php

namespace Database\Factories;

use App\Enums\Devices;
use App\Enums\MeasureUnits;
use App\Models\Activation;
use App\Models\Deviation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activation>
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
    public function definition(): array
    {
        return [
            'device' => fake()->randomElement(Devices::values()),
            'active_until' => function (array $attributes) {
                return fake()->dateTimeBetween(Carbon::now(), Carbon::now()->addHour());
            },
            'deviation_id' => Deviation::factory(),
            'amount' => fake()->randomFloat(1, 0, 1200),
            'measure_unit' => fake()->randomElement(MeasureUnits::values()),
        ];
    }
}
