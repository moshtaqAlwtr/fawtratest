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
        Schema::create('manufactur_orders_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('manufactur_order_id')->index('manufactur_orders_items_manufactur_order_id_foreign');
            $table->unsignedBigInteger('raw_product_id')->index('manufactur_orders_items_raw_product_id_foreign');
            $table->unsignedBigInteger('raw_production_stage_id')->index('manufactur_orders_items_raw_production_stage_id_foreign');
            $table->decimal('raw_unit_price', 10)->nullable();
            $table->integer('raw_quantity')->nullable();
            $table->decimal('raw_total', 10)->nullable();
            $table->unsignedBigInteger('expenses_account_id')->index('manufactur_orders_items_expenses_account_id_foreign');
            $table->unsignedBigInteger('expenses_production_stage_id')->index('manufactur_orders_items_expenses_production_stage_id_foreign');
            $table->tinyInteger('expenses_cost_type')->nullable()->default(1);
            $table->decimal('expenses_price', 10)->nullable();
            $table->string('expenses_description')->nullable();
            $table->decimal('expenses_total', 10)->nullable();
            $table->unsignedBigInteger('workstation_id')->index('manufactur_orders_items_workstation_id_foreign');
            $table->integer('operating_time')->nullable();
            $table->unsignedBigInteger('manu_production_stage_id')->index('manufactur_orders_items_manu_production_stage_id_foreign');
            $table->tinyInteger('manu_cost_type')->nullable()->default(1);
            $table->decimal('manu_total_cost', 10)->nullable();
            $table->string('manu_description')->nullable();
            $table->decimal('manu_total', 10)->nullable();
            $table->unsignedBigInteger('end_life_product_id')->index('manufactur_orders_items_end_life_product_id_foreign');
            $table->unsignedBigInteger('end_life_production_stage_id')->index('manufactur_orders_items_end_life_production_stage_id_foreign');
            $table->integer('end_life_unit_price')->nullable();

            $table->integer('end_life_quantity')->nullable();
            $table->decimal('end_life_total', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufactur_orders_items');
    }
};
