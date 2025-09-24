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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->unique();
            $table->enum('type', ['invoice', 'Return', 'Requested', 'City Notice'])->nullable()->default('Requested')->comment('1: Purchase Order, 2: Purchase Invoice, 3: Purchase Return, 4: Credit Note');
            $table->unsignedBigInteger('reference_id')->nullable()->index('purchase_invoices_reference_id_foreign');
            $table->enum('receiving_status', ['not_received', 'received', 'partially_received'])->nullable()->default('not_received')->comment('0: Not Received, 1: Partially Received, 2: Received');
            $table->unsignedBigInteger('supplier_id')->nullable()->index('purchase_invoices_supplier_id_foreign');
            $table->unsignedBigInteger('account_id')->nullable()->index('purchase_invoices_account_id_foreign');
            $table->date('date')->nullable();
            $table->integer('terms')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'processing', 'refunded'])->nullable()->default('unpaid')->comment('                Purchase Order: 1:Draft, 2:Pending, 3:Approved, 4:Converted to Invoice, 5:Cancelled
                Purchase Invoice: 1:Draft, 2:Pending, 3:Approved, 4:Paid, 5:Partially Paid, 6:Cancelled
                Purchase Return: 1:Draft, 2:Pending, 3:Approved, 4:Completed, 5:Cancelled
            ');
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->string('discount_percentage')->nullable();
            $table->tinyInteger('discount_type')->nullable()->default(1);
            $table->decimal('advance_payment', 10)->nullable()->default(0);
            $table->tinyInteger('advance_payment_type')->nullable()->default(1);
            $table->boolean('is_paid')->nullable()->default(false);
            $table->tinyInteger('payment_method')->nullable()->default(1);
            $table->string('reference_number')->nullable();
            $table->tinyInteger('tax_type')->nullable()->default(1);
            $table->decimal('shipping_cost', 10)->nullable()->default(0);
            $table->decimal('subtotal', 15)->nullable()->default(0);
            $table->decimal('due_value', 15)->nullable();
            $table->decimal('total_discount', 15)->nullable()->default(0);
            $table->decimal('total_tax', 15)->nullable()->default(0);
            $table->decimal('grand_total', 15)->nullable()->default(0);
            $table->enum('status', ['disagree', 'approval', 'Under Review', 'convert invoice'])->nullable()->default('Under Review');
            $table->text('notes')->nullable();
            $table->boolean('is_received')->nullable()->default(false);
            $table->date('received_date')->nullable();
            $table->string('attachments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_invoices_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('purchase_invoices_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
