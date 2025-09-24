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
        Schema::table('indirect_cost_items', function (Blueprint $table) {
            $table->foreign(['indirect_costs_id'])->references(['id'])->on('indirect_costs')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['manufacturing_order_id'])->references(['id'])->on('manufactur_orders')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indirect_cost_items', function (Blueprint $table) {
            $table->dropForeign('indirect_cost_items_indirect_costs_id_foreign');
            $table->dropForeign('indirect_cost_items_manufacturing_order_id_foreign');
        });
    }
};
