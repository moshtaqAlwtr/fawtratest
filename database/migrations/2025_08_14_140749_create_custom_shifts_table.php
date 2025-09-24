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
        Schema::create('custom_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedBigInteger('shift_id')->index('custom_shifts_shift_id_foreign');
            $table->tinyInteger('use_rules')->default(1)->comment('(قواعد-1) (2-موظفين)');
            $table->unsignedBigInteger('branch_id')->nullable()->index('custom_shifts_branch_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('custom_shifts_department_id_foreign');
            $table->unsignedBigInteger('job_title_id')->nullable()->index('custom_shifts_job_title_id_foreign');
            $table->unsignedBigInteger('shift_rule_id')->nullable()->index('custom_shifts_shift_rule_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_shifts');
    }
};
