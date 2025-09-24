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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sub_account')->nullable();
            $table->integer('storehouse_id')->nullable();
            $table->integer('price_list_id')->nullable();
            $table->tinyInteger('enable_negative_stock')->default(0);
            $table->tinyInteger('advanced_pricing_options')->default(0);
            $table->tinyInteger('enable_stock_requests')->default(0);
            $table->tinyInteger('enable_sales_stock_authorization')->default(0);
            $table->tinyInteger('enable_purchase_stock_authorization')->default(0);
            $table->tinyInteger('track_products_by_serial_or_batch')->default(0);
            $table->tinyInteger('allow_negative_tracking_elements')->default(0);
            $table->tinyInteger('enable_multi_units_system')->default(0);
            $table->tinyInteger('inventory_quantity_by_date')->default(0);
            $table->tinyInteger('enable_assembly_and_compound_units')->default(0);
            $table->tinyInteger('show_available_quantity_in_warehouse')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
