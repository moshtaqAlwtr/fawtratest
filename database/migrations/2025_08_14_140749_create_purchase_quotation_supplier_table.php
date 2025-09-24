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
        Schema::create('purchase_quotation_supplier', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_quotation_id');
            $table->unsignedBigInteger('supplier_id')->index('purchase_quotation_supplier_supplier_id_foreign');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->index('purchase_quotation_supplier_purchase_order_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_quotation_supplier_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('purchase_quotation_supplier_updated_by_foreign');
            $table->timestamps();

            $table->unique(['purchase_quotation_id', 'supplier_id'], 'quote_supplier_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotation_supplier');
    }
};
