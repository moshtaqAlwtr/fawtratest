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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('employee_photo')->nullable();
            $table->text('notes')->nullable();
            $table->string('email')->unique();
            $table->tinyInteger('employee_type')->default(1)->comment('(2 مستخدم)(1 موظف)');
            $table->tinyInteger('status')->default(1)->comment('(1 نشط)(2 غير نشط)');
            $table->tinyInteger('allow_system_access')->default(0);
            $table->tinyInteger('send_credentials')->default(0);
            $table->tinyInteger('language')->default(1);
            $table->unsignedBigInteger('Job_role_id')->index('employees_job_role_id_foreign');
            $table->integer('access_branches_id')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->tinyInteger('gender')->default(1)->comment('[1-male, 2-female]');
            $table->tinyInteger('nationality_status')->default(1);
            $table->tinyInteger('country')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('current_address_1')->nullable();
            $table->string('current_address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedBigInteger('job_title_id')->nullable()->index('employees_job_title_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('employees_department_id_foreign');
            $table->unsignedBigInteger('job_level_id')->nullable()->index('employees_job_level_id_foreign');
            $table->unsignedBigInteger('job_type_id')->nullable()->index('employees_job_type_id_foreign');
            $table->unsignedBigInteger('branch_id')->nullable()->index('employees_branch_id_foreign');
            $table->unsignedBigInteger('direct_manager_id')->nullable()->index('employees_direct_manager_id_foreign');
            $table->date('hire_date')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable()->index('employees_shift_id_foreign');
            $table->integer('created_by');
            $table->integer('custom_financial_month')->nullable();
            $table->integer('custom_financial_day')->nullable();
            $table->string('leave_policy', 100)->nullable();
            $table->string('attendance_rate', 100)->nullable();
            $table->string('attendance_shifts', 100)->nullable();
            $table->string('holiday_lists', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
