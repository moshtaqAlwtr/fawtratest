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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreign(['credit_note_id'])->references(['id'])->on('credit_notifications')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['periodic_invoice_id'])->references(['id'])->on('periodic_invoices')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['purchase_invoice_id'])->references(['id'])->on('purchase_invoices')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['quotation_id'])->references(['id'])->on('quotes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['quote_id'])->references(['id'])->on('quotes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['store_house_id'])->references(['id'])->on('store_houses')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign('invoice_items_credit_note_id_foreign');
            $table->dropForeign('invoice_items_invoice_id_foreign');
            $table->dropForeign('invoice_items_periodic_invoice_id_foreign');
            $table->dropForeign('invoice_items_product_id_foreign');
            $table->dropForeign('invoice_items_purchase_invoice_id_foreign');
            $table->dropForeign('invoice_items_quotation_id_foreign');
            $table->dropForeign('invoice_items_quote_id_foreign');
            $table->dropForeign('invoice_items_store_house_id_foreign');
        });
    }
};
