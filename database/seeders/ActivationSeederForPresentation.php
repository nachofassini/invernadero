<?php

namespace Database\Seeders;

use App\Models\Activation;
use App\Models\Measure;
use App\Models\Stage;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivationSeederForPresentation extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $creationDate = Carbon::create(2024, 1, 1);

        // 20 lights activations
        Activation::factory()->count(21)->create([
            'created_at' => $creationDate,
            'updated_at' => $creationDate,
            'active_until' =>  $creationDate->copy()->addMinutes(60),
            'amount' => 60,
            'measure_unit' => 'Mins.',
            'device' => Activation::DEVICE_LIGHT,
            'activated_by' => Activation::LOW_LIGHTING,
            'deviation' => ['expected' => 50, 'obtained' => 49.2],
            'measure_id' => null,
        ]);

        // 6 high humidity activations
        Activation::factory()->count(6)->create([
            'created_at' => $creationDate,
            'updated_at' => $creationDate,
            'active_until' =>  $creationDate->copy()->addMinutes(1),
            'amount' => 1,
            'measure_unit' => 'Mins.',
            'device' => Activation::DEVICE_EXTRACTOR,
            'activated_by' => Activation::HIGH_HUMIDITY,
            'deviation' => ['expected' => 80, 'obtained' => 82],
            'measure_id' => null,
        ]);

        // 4 soil humidity activations
        Activation::factory()->count(4)->create([
            'created_at' => $creationDate,
            'updated_at' => $creationDate,
            'active_until' =>  $creationDate->copy()->addMinutes(0.5),
            'amount' => 0.5,
            'measure_unit' => 'Mins.',
            'device' => Activation::DEVICE_WATER,
            'activated_by' => Activation::LOW_SOIL_HUMIDITY,
            'deviation' => ['expected' => 20, 'obtained' => 19.2],
            'measure_id' => null,
        ]);
    }
}
