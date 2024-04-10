<?php

namespace Database\Seeders;

use App\Models\Activation;
use App\Models\Measure;
use App\Models\Stage;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $activeStage = Stage::first();
        $newMeasure = Measure::factory()->create(['created_at' => $now, 'updated_at' => $now, 'inside_temperature' => $activeStage->max_temperature + 1]);

        Activation::factory()->create([
            "measure_id" => $newMeasure->id,
            'device' => 'fan',
            'activated_by' => 'high_temp',
            'amount' => 5.5,
            'measure_unit' => 'Mins.',
            'active_until' => $now->addMinutes(5),
        ]);
    }
}
