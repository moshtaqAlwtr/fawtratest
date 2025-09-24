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
        Schema::create('attendance_sheets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('from_date');
            $table->date('to_date');
            $table->tinyInteger('use_rules')->default(1)->comment('(قواعد-1) (2-موظفين)');
            $table->tinyInteger('status')->default(0)->comment('(0- تحت المراجعه),(1-موافق علية)');
            $table->unsignedBigInteger('branch_id')->nullable()->index('attendance_sheets_branch_id_foreign');
            $table->unsignedBigInteger('department_id')->nullable()->index('attendance_sheets_department_id_foreign');
            $table->unsignedBigInteger('job_title_id')->nullable()->index('attendance_sheets_job_title_id_foreign');
            $table->unsignedBigInteger('shift_id')->nullable()->index('attendance_sheets_shift_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sheets');
    }
};
