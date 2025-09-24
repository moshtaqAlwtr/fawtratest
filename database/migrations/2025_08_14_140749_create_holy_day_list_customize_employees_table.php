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
        Schema::create('holy_day_list_customize_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('holyday_customizes_id')->index('holy_day_list_customize_employees_holyday_customizes_id_foreign');
            $table->unsignedBigInteger('employee_id')->index('holy_day_list_customize_employees_employee_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holy_day_list_customize_employees');
    }
};
