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
        Schema::create('custom_shifts_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('custom_shifts_id')->index('custom_shifts_employees_custom_shifts_id_foreign');
            $table->unsignedBigInteger('employee_id')->index('custom_shifts_employees_employee_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_shifts_employees');
    }
};
