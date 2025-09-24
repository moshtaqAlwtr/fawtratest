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
        Schema::create('shift_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shift_id')->index('shift_days_shift_id_foreign');
            $table->enum('day', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->tinyInteger('working_day')->default(0);
            $table->time('start_time');
            $table->time('end_time');
            $table->time('login_start_time');
            $table->time('login_end_time');
            $table->time('logout_start_time');
            $table->time('logout_end_time');
            $table->integer('grace_period');
            $table->integer('delay_calculation')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_days');
    }
};
