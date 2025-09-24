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
        Schema::create('payments_process', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('payments_process_client_id_foreign');
            $table->unsignedBigInteger('purchases_id')->nullable()->index('payments_process_purchases_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('payments_process_invoice_id_foreign');
            $table->unsignedBigInteger('installments_id')->nullable()->index('payments_process_installments_id_foreign');
            $table->unsignedBigInteger('supplier_id')->nullable()->index('payments_process_supplier_id_foreign');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable()->index('payments_process_account_id_foreign');
            $table->dateTime('payment_date')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('payment_status')->nullable()->default(1);
            $table->tinyInteger('Payment_method')->nullable()->default(1)->comment('1: cash, 2: check, 3: Pinky, 4:online, 5: others');
            $table->string('reference_number')->nullable();
            $table->string('payment_data')->nullable();
            $table->text('notes')->nullable();
            $table->text('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_process');
    }
};
