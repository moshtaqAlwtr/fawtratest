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
        Schema::create('leave_policy_customizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('use_rules')->default(1)->comment('(قواعد-1) (2-موظفين)');
            $table->unsignedBigInteger('leave_policy_id')->index('leave_policy_customizes_leave_policy_id_foreign');
            $table->unsignedBigInteger('branch_id')->nullable()->index('leave_policy_customizes_branch_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('leave_policy_customizes_department_id_foreign');
            $table->unsignedBigInteger('job_title_id')->nullable()->index('leave_policy_customizes_job_title_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_policy_customizes');
    }
};
