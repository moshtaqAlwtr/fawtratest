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
        Schema::create('leave_policy_customize_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('policy_customize_id')->index('leave_policy_customize_employees_policy_customize_id_foreign');
            $table->unsignedBigInteger('employee_id')->index('leave_policy_customize_employees_employee_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_policy_customize_employees');
    }
};
