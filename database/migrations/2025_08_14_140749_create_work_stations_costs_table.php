<?php

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
        Schema::create('work_stations_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_station_id')->index('work_stations_costs_work_station_id_foreign');
            $table->decimal('cost_expenses', 10)->nullable();
            $table->integer('account_expenses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_stations_costs');
    }
};
