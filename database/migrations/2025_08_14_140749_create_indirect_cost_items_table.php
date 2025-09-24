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
        Schema::create('indirect_cost_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('indirect_costs_id')->index('indirect_cost_items_indirect_costs_id_foreign');
            $table->unsignedBigInteger('restriction_id');
            $table->decimal('restriction_total', 10);
            $table->unsignedBigInteger('manufacturing_order_id')->index('indirect_cost_items_manufacturing_order_id_foreign');
            $table->decimal('manufacturing_price', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indirect_cost_items');
    }
};
