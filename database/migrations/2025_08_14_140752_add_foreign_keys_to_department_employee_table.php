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
        Schema::table('department_employee', function (Blueprint $table) {
            $table->foreign(['department_id'])->references(['id'])->on('departments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_employee', function (Blueprint $table) {
            $table->dropForeign('department_employee_department_id_foreign');
            $table->dropForeign('department_employee_employee_id_foreign');
        });
    }
};
