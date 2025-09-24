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
        Schema::create('installment_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('salary_advance_id')->nullable();
            $table->string('installment_number', 200)->nullable();
            $table->double('amount')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 200)->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
    }
};
