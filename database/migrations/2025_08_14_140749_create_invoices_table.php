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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('invoices_client_id_foreign');
            $table->unsignedBigInteger('employee_id')->nullable()->index('invoices_employee_id_foreign');
            $table->text('qrcode')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable()->index('invoices_treasury_id_foreign');
            $table->tinyInteger('payment')->nullable()->default(1)->comment('1: print, 2: send to client');
            $table->string('code')->nullable()->unique();
            $table->date('invoice_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('payment_terms', 100)->nullable();
            $table->tinyInteger('payment_status')->nullable()->default(1)->comment('1: Unpaid, 2: Partially Paid, 3: Fully Paid');
            $table->string('currency', 10)->nullable()->default('SAR');
            $table->decimal('due_value', 10)->nullable();
            $table->decimal('total', 10)->nullable();
            $table->decimal('grand_total', 10)->nullable();
            $table->decimal('advance_payment', 10)->nullable()->default(0);
            $table->decimal('remaining_amount', 10)->nullable();
            $table->boolean('is_paid')->nullable()->default(false);
            $table->tinyInteger('payment_method')->nullable()->default(1)->comment('1: Cash, 2: Bank Transfer, 3: Credit Card, 4: Check, 5: Other');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->decimal('shipping_cost', 10)->nullable()->default(0);
            $table->decimal('shipping_tax', 10)->nullable()->default(0);
            $table->string('tax_type', 50)->nullable();
            $table->decimal('tax_total', 10)->nullable();
            $table->bigInteger('subscription_id')->nullable();
            $table->string('attachments')->nullable();
            $table->string('type', 50)->nullable();
            $table->float('subtotal')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('invoices_created_by_foreign');
            $table->string('adjustment_label', 200)->nullable();
            $table->string('adjustment_type', 200)->nullable();
            $table->float('adjustment_value')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index('invoices_updated_by_foreign');
            $table->timestamps();
            $table->double('returned_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
