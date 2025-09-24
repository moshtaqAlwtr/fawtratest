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
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('contracts_employee_id_foreign');
            $table->unsignedBigInteger('job_title_id')->index('contracts_job_title_id_foreign');
            $table->unsignedBigInteger('job_level_id')->index('contracts_job_level_id_foreign');
            $table->unsignedBigInteger('salary_temp_id')->index('contracts_salary_temp_id_foreign');
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('parent_contract_id')->nullable()->index('contracts_parent_contract_id_foreign');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('type_contract')->nullable()->default(1)->comment('(1=>period) (2=>end_date)');
            $table->tinyInteger('duration_unit')->nullable()->default(1)->comment('(1=>day) (2=>month) (3=>year)');
            $table->integer('duration')->nullable();
            $table->integer('amount')->nullable();
            $table->date('join_date');
            $table->date('probation_end_date');
            $table->date('contract_date');
            $table->tinyInteger('receiving_cycle')->nullable()->default(1)->comment('1=>monthly, 2=>weekly, 3=>yearly , 4=>Quarterly,5=>Once a week');
            $table->string('currency', 3)->default('SAR');
            $table->string('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
