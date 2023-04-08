<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('active_until')->nullable();
            $table->enum('device', ['fan', 'extractor', 'light', 'irrigation']);
            $table->enum('activated_by', ['low_temp', 'high_temp', 'low_humidity', 'high_humidity', 'low_soil_humidity', 'high_soil_humidity', 'low_lighting', 'low_co2', 'high_co2', 'manual']);
            $table->foreignId('measure_id')->nullable()->constrained();
            $table->double('amount', 6, 2)->nullable(); // value of the amount of water/ minutes of vent / hs of light delivered / etc...
            $table->enum('measure_unit', ['mm3', 'm3', '%', 'Hs.', 'Mins.', 'ppm', 'ºC'])->nullable(); // mesaure unit for the amount delivered (mm3 for water, mins for vent, hs for lighting, etc..)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activations');
    }
};
