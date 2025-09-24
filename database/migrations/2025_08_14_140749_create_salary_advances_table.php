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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('submission_date');
            $table->decimal('amount', 10);
            $table->tinyInteger('currency')->nullable()->default(1)->comment('1=SAR, 2=USD, 3=EUR, 4=GBP, 5=CNY');
            $table->decimal('installment_amount', 10);
            $table->integer('total_installments');
            $table->tinyInteger('payment_rate')->nullable()->default(1)->comment('1- Monthly, 2- Weekly, 3- Daily');
            $table->integer('paid_installments')->default(0);
            $table->date('installment_start_date');
            $table->unsignedBigInteger('treasury_id')->index('salary_advances_treasury_id_foreign');
            $table->boolean('pay_from_salary')->default(false);
            $table->string('tag')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
