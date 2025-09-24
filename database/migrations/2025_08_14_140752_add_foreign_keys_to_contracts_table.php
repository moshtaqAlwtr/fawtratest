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
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['job_level_id'])->references(['id'])->on('functional_levels')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['job_title_id'])->references(['id'])->on('jop_titles')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['parent_contract_id'])->references(['id'])->on('contracts')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['salary_temp_id'])->references(['id'])->on('salary_template')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign('contracts_employee_id_foreign');
            $table->dropForeign('contracts_job_level_id_foreign');
            $table->dropForeign('contracts_job_title_id_foreign');
            $table->dropForeign('contracts_parent_contract_id_foreign');
            $table->dropForeign('contracts_salary_temp_id_foreign');
        });
    }
};
