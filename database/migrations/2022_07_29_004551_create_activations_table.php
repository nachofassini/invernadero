<?php

use App\Models\Deviation;
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
        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('active_until')->nullable();
            $table->enum('device', \App\Enums\Devices::values());
            $table->foreignIdFor(Deviation::class)->nullable();
            $table->double('amount')->nullable(); // value of the amount of water/ minutes of vent / hs of light delivered / etc...
            $table->enum('measure_unit', \App\Enums\MeasureUnits::values())->nullable(); // measure unit for the amount delivered (mm3 for water, mins for vent, hs for lighting, etc..)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activations');
    }
};
