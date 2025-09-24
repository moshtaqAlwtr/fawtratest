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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('invoice_items_invoice_id_foreign');
            $table->unsignedBigInteger('quotation_id')->nullable()->index('invoice_items_quotation_id_foreign');
            $table->unsignedBigInteger('product_id')->nullable()->index('invoice_items_product_id_foreign');
            $table->unsignedBigInteger('packege_id')->nullable();
            $table->unsignedBigInteger('store_house_id')->nullable()->index('invoice_items_store_house_id_foreign');
            $table->unsignedBigInteger('quote_id')->nullable()->index('invoice_items_quote_id_foreign');
            $table->unsignedBigInteger('credit_note_id')->nullable()->index('invoice_items_credit_note_id_foreign');
            $table->unsignedBigInteger('periodic_invoice_id')->nullable()->index('invoice_items_periodic_invoice_id_foreign');
            $table->integer('quotes_purchase_order_id')->nullable();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable()->index('invoice_items_purchase_invoice_id_foreign');
            $table->bigInteger('purchase_order_id')->nullable();
            $table->tinyInteger('purchase_invoice_id_type')->nullable()->default(1)->comment('1: Purchase Order, 2: Purchase Invoice, 3: Purchase Return, 4: Credit Note');
            $table->string('item');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('discount', 10)->nullable();
            $table->bigInteger('purchase_quotation_id')->nullable();
            $table->tinyInteger('discount_type')->default(1)->comment('1=>percentage 2=>currency');
            $table->decimal('tax_1', 5)->nullable();
            $table->decimal('tax_2', 5)->nullable();
            $table->decimal('total', 10)->nullable();
            $table->enum('type', ['product', 'packege'])->default('product');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
