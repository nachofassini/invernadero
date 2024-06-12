<?php

use App\Models\Measure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deviations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', \App\Enums\DeviationTypes::values());
            $table->double('expected');
            $table->double('observed');
            $table->foreignIdFor(Measure::class, 'detection_id')->constrained()->references('id')->on('measures');
            $table->foreignIdFor(Measure::class, 'fix_id')->nullable()->constrained()->references('id')->on('measures');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deviations');
    }
};
