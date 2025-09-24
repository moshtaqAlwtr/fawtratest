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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->date('registration_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('select_emp_role')->nullable()->default(1)->comment('(1=>employee) (2=>role)');
            $table->tinyInteger('receiving_cycle')->nullable()->default(1)->comment('1=>monthly, 2=>weekly, 3=>yearly , 4=>Quarterly,5=>Once a week');
            $table->boolean('attendance_check')->default(false);
            $table->unsignedBigInteger('department_id')->nullable()->index('payrolls_department_id_foreign');
            $table->unsignedBigInteger('jop_title_id')->nullable()->index('payrolls_jop_title_id_foreign');
            $table->unsignedBigInteger('branch_id')->nullable()->index('payrolls_branch_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
