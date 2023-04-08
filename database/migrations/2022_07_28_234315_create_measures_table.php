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
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->double('consumption', 4, 1);
            $table->double('inside_temperature', 4, 1);
            $table->double('outside_temperature', 4, 1);
            $table->double('inside_humidity', 4, 1);
            $table->double('outside_humidity', 4, 1);
            $table->double('soil_humidity', 4, 1);
            $table->unsignedSmallInteger('co2');
            $table->double('lighting', 4, 1);
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
