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
        Schema::table('attendance_sheets_employees', function (Blueprint $table) {
            $table->foreign(['attendance_sheets_id'])->references(['id'])->on('attendance_sheets')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_sheets_employees', function (Blueprint $table) {
            $table->dropForeign('attendance_sheets_employees_attendance_sheets_id_foreign');
            $table->dropForeign('attendance_sheets_employees_employee_id_foreign');
        });
    }
};
