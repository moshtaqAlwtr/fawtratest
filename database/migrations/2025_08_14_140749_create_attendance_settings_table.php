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
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('start_month');
            $table->unsignedTinyInteger('start_day');
            $table->boolean('allow_second_shift')->default(false);
            $table->boolean('allow_backdated_requests')->default(false);
            $table->boolean('direct_manager_approval')->default(false);
            $table->boolean('department_manager_approval')->default(false);
            $table->boolean('employees_approval')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
