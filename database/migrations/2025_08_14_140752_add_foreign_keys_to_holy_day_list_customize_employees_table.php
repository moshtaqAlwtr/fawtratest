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
        Schema::table('holy_day_list_customize_employees', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['holyday_customizes_id'])->references(['id'])->on('holy_day_list_customizes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holy_day_list_customize_employees', function (Blueprint $table) {
            $table->dropForeign('holy_day_list_customize_employees_employee_id_foreign');
            $table->dropForeign('holy_day_list_customize_employees_holyday_customizes_id_foreign');
        });
    }
};
