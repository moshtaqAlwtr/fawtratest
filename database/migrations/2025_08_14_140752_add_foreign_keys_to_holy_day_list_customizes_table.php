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
        Schema::table('holy_day_list_customizes', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['department_id'])->references(['id'])->on('departments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['holiday_list_id'])->references(['id'])->on('holiday_lists')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['job_title_id'])->references(['id'])->on('jop_titles')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holy_day_list_customizes', function (Blueprint $table) {
            $table->dropForeign('holy_day_list_customizes_branch_id_foreign');
            $table->dropForeign('holy_day_list_customizes_department_id_foreign');
            $table->dropForeign('holy_day_list_customizes_holiday_list_id_foreign');
            $table->dropForeign('holy_day_list_customizes_job_title_id_foreign');
        });
    }
};
