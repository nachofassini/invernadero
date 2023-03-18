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
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->double('consumption', 5, 2);
            $table->double('outside_temperature', 5, 2);
            $table->double('outside_humidity', 5, 2);
            $table->double('inside_temperature', 5, 2);
            $table->double('inside_humidity', 5, 2);
            $table->double('soil_humidity', 5, 2);
            $table->double('co2', 5, 2);
            $table->double('lighting', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measures');
    }
};
