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
        Schema::create('warehouse_permits_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->integer('stock_before')->default(0);
            $table->integer('stock_after')->default(0);
            $table->decimal('total', 10);
            $table->decimal('unit_price', 10);
            $table->unsignedBigInteger('warehouse_permits_id')->index('warehouse_permits_products_warehouse_permits_id_foreign');
            $table->unsignedBigInteger('product_id')->index('warehouse_permits_products_product_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_permits_products');
    }
};
