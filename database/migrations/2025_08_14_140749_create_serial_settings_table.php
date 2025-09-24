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
        Schema::create('serial_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section')->unique();
            $table->integer('current_number')->default(0);
            $table->integer('number_of_digits')->default(5);
            $table->string('prefix')->nullable();
            $table->integer('mode')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_settings');
    }
};
