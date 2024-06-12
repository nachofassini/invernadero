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
        Schema::create('measures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->double('consumption')->nullable();
            $table->double('inside_temperature');
            $table->double('outside_temperature');
            $table->double('inside_humidity');
            $table->double('outside_humidity');
            $table->double('soil_humidity');
            $table->unsignedSmallInteger('co2')->nullable();
            $table->double('lighting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measures');
    }
};
