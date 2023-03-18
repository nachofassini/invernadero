<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->enum('device', ['intractor', 'extractor', 'ventilation', 'light', 'irrigation']);
            $table->enum('activated_by', ['low_temp', 'high_temp', 'low_humidity', 'high_humidity', 'low_soil_humidity', 'high_soil_humidity', 'low_lighting', 'low_co2', 'high_co2']);
            $table->foreignId('measure_id')->constrained();
            $table->boolean('enabled');
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
