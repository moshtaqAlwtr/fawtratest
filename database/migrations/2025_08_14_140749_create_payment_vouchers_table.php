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
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('account_id')->nullable()->index('payment_vouchers_account_id_foreign');
            $table->unsignedBigInteger('treasury_id')->nullable()->index('payment_vouchers_treasury_id_foreign');
            $table->unsignedBigInteger('employee_id')->nullable()->index('payment_vouchers_employee_id_foreign');
            $table->unsignedBigInteger('tax_id')->nullable()->index('payment_vouchers_tax_id_foreign');
            $table->date('voucher_date');
            $table->string('payee_name');
            $table->decimal('amount', 15);
            $table->decimal('tax_amount', 15)->default(0);
            $table->enum('voucher_type', ['expense', 'income'])->default('expense');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
