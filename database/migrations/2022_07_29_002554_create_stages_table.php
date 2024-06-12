<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('crop_id')->constrained();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->unsignedSmallInteger('days');
            $table->double('min_temperature');
            $table->double('max_temperature');
            $table->double('min_humidity');
            $table->double('max_humidity');
            $table->double('min_co2');
            $table->double('max_co2');
            $table->unsignedInteger('irrigation');
            $table->double('light_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
