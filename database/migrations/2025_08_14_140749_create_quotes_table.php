<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('quotes_client_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('quotes_created_by_foreign');
            $table->date('quote_date')->nullable();
            $table->string('quote_number')->nullable();
            $table->decimal('subtotal', 10)->nullable();
            $table
                ->enum('status', ['Draft', 'Pending', 'Approved', 'Converted_to_Invoice', 'Cancelled'])
                ->default('Draft')
                ->comment('Draft, Pending, Approved, Converted to Invoice, Cancelled');

            $table->decimal('total_discount', 10)->nullable();
            $table->decimal('due_value', 10)->nullable();
            $table->decimal('total_tax', 10)->nullable();
            $table->decimal('shipping_cost', 10)->nullable();
            $table->decimal('next_payment', 10)->nullable();
            $table->decimal('grand_total', 10)->nullable();
            $table->text('notes')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 10)->nullable();
            $table->string('tax_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
