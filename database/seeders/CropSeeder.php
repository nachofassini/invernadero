<?php

namespace Database\Seeders;

use App\Models\Crop;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $strawberryCrop = Crop::create(['name' => 'Frutilla', 'active_since' => Carbon::now()->subDays(45)]);
        Crop::create(['name' => 'Banana']);
        Crop::create(['name' => 'Lechuga']);
        Crop::create(['name' => 'Tomate']);

        $strawberryCrop->stages()->createMany([
            [
                'name' => 'Germinación',
                'order' => 1,
                'days' => 5,
                'min_temperature' => 20,
                'max_temperature' => 30,
                'min_humidity' => 30,
                'max_humidity' => 100,
                'min_co2' => 700,
                'max_co2' => 700,
                'light_hours' => 12,
                'irrigation' => 200,
            ],
            [
                'name' => 'Crecimiento',
                'order' => 2,
                'days' => 15,
                'min_temperature' => 22.5,
                'max_temperature' => 32.5,
                'min_humidity' => 20,
                'max_humidity' => 100,
                'min_co2' => 700,
                'max_co2' => 700,
                'light_hours' => 12,
                'irrigation' => 300,
            ],
            [
                'name' => 'Maduración',
                'order' => 3,
                'days' => 10,
                'min_temperature' => 22.5,
                'max_temperature' => 30,
                'min_humidity' => 20,
                'max_humidity' => 70,
                'min_co2' => 700,
                'max_co2' => 700,
                'light_hours' => 10,
                'irrigation' => 100,
            ],
        ]);
    }
}
