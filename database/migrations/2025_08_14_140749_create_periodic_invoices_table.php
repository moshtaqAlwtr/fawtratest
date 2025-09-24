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
        Schema::create('periodic_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('client_id')->nullable()->index('periodic_invoices_client_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('periodic_invoices_created_by_foreign');
            $table->string('details_subscription');
            $table->date('first_invoice_date');
            $table->integer('repeat_count');
            $table->tinyInteger('repeat_type')->default(1)->comment('1=>weekly, 2=>bi-weekly, 3=>monthly, 4=>bi-monthly, 5=>yearly, 6=>annual');
            $table->integer('repeat_interval')->default(1);
            $table->integer('invoice_days_offset')->default(0);
            $table->decimal('total', 10);
            $table->decimal('grand_total', 10)->default(0);
            $table->decimal('subtotal', 10)->nullable();
            $table->tinyInteger('status')->nullable()->default(1)->comment('1:Draft, 2:Pending, 3:Approved, 4:Converted to Invoice, 5:Cancelled');
            $table->decimal('total_discount', 10)->nullable();
            $table->decimal('total_tax', 10)->nullable();
            $table->decimal('shipping_cost', 10)->nullable();
            $table->text('notes')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 10)->nullable();
            $table->string('tax_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_generate')->default(false);
            $table->boolean('show_from_to_dates')->default(false);
            $table->boolean('disable_partial_payment')->default(false);
            $table->string('payment_terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_invoices');
    }
};
