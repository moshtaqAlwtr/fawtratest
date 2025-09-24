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
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('salary_slips_employee_id_foreign');
            $table->date('slip_date');
            $table->string('status')->nullable()->default('cancel');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('currency', 10);
            $table->decimal('total_salary', 10)->default(0);
            $table->decimal('total_deductions', 10)->default(0);
            $table->decimal('net_salary', 10)->default(0);
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
