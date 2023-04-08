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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('crop_id')->constrained();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->unsignedSmallInteger('days');
            $table->double('min_temperature', 5, 2);
            $table->double('max_temperature', 5, 2);
            $table->double('min_humidity', 5, 2);
            $table->double('max_humidity', 5, 2);
            $table->double('min_co2', 5, 2);
            $table->double('max_co2', 5, 2);
            $table->unsignedInteger('irrigation');
            $table->double('light_hours', 4, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stages');
    }
};
