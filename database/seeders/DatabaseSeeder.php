<?php

namespace Database\Seeders;

use App\Models\Activation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CropSeeder::class);
        $this->call(MeasureSeeder::class);

        Activation::factory(10)->create();
    }
}
