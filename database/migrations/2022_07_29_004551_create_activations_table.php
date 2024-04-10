<?php

use App\Models\Activation;
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
        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('active_until')->nullable();
            $table->enum('device', ACTIVATION::DEVICES);
            $table->enum('activated_by', Activation::TYPES);
            $table->foreignId('measure_id')->nullable()->constrained();
            $table->double('amount', 6, 2)->nullable(); // value of the amount of water/ minutes of vent / hs of light delivered / etc...
            $table->enum('measure_unit', Activation::MEASURE_UNITS)->nullable(); // measure unit for the amount delivered (mm3 for water, mins for vent, hs for lighting, etc..)
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
