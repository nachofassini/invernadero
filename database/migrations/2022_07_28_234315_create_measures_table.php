<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
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
            $table->double('consumption', 6, 2)->nullable();;
            $table->double('inside_temperature', 6, 2);
            $table->double('outside_temperature', 6, 2);
            $table->double('inside_humidity', 6, 2);
            $table->double('outside_humidity', 6, 2);
            $table->double('soil_humidity', 6, 2);
            $table->unsignedSmallInteger('co2')->nullable();
            $table->double('lighting', 6, 2);
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
