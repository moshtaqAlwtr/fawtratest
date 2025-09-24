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
        Schema::create('holy_day_list_customizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('use_rules')->default(1)->comment('(قواعد-1) (2-موظفين)');
            $table->unsignedBigInteger('holiday_list_id')->index('holy_day_list_customizes_holiday_list_id_foreign');
            $table->unsignedBigInteger('branch_id')->nullable()->index('holy_day_list_customizes_branch_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('holy_day_list_customizes_department_id_foreign');
            $table->unsignedBigInteger('job_title_id')->nullable()->index('holy_day_list_customizes_job_title_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holy_day_list_customizes');
    }
};
