<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $strawberryCrop = Crop::create(['name' => 'Frutilla', 'active' => true]);
        Crop::create(['name' => 'Banana', 'active' => false]);
        Crop::create(['name' => 'Lechuga', 'active' => false]);
        Crop::create(['name' => 'Tomate', 'active' => false]);

        $strawberryCrop->stages()->createMany([
            [
                'name' => 'Germinación',
                'days' => 30,
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
                'days' => 45,
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
                'days' => 20,
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
