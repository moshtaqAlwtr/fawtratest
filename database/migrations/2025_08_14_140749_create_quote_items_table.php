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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quote_id')->index('quote_items_quote_id_foreign');
            $table->unsignedBigInteger('product_id')->index('quote_items_product_id_foreign');
            $table->string('item');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10);
            $table->integer('quantity');
            $table->decimal('discount', 10)->default(0);
            $table->decimal('tax_1', 5)->default(0);
            $table->decimal('tax_2', 5)->default(0);
            $table->decimal('total', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
