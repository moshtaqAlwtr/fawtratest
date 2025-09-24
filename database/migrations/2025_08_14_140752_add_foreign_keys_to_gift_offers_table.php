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
        Schema::table('gift_offers', function (Blueprint $table) {
            $table->foreign(['target_product_id'], 'gift_offers_ibfk_1')->references(['id'])->on('products')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['gift_product_id'], 'gift_offers_ibfk_2')->references(['id'])->on('products')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_offers', function (Blueprint $table) {
            $table->dropForeign('gift_offers_ibfk_1');
            $table->dropForeign('gift_offers_ibfk_2');
        });
    }
};
