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
        Schema::table('custom_shifts_employees', function (Blueprint $table) {
            $table->foreign(['custom_shifts_id'])->references(['id'])->on('custom_shifts')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_shifts_employees', function (Blueprint $table) {
            $table->dropForeign('custom_shifts_employees_custom_shifts_id_foreign');
            $table->dropForeign('custom_shifts_employees_employee_id_foreign');
        });
    }
};
