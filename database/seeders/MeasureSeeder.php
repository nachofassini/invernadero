<?php

namespace Database\Seeders;

use App\Models\Measure;
use App\Models\Stage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeasureSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stage::all()->first(function (Stage $stage) {
            $period = CarbonPeriod::create(Carbon::now()->subDays($stage->days), '1 minute', Carbon::now());
            foreach ($period as $dt) {
                Measure::factory()->create([
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);
            }
        });
    }
}
