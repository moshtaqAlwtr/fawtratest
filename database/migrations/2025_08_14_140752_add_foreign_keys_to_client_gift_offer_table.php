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
        Schema::table('client_gift_offer', function (Blueprint $table) {
            $table->foreign(['client_id'], 'client_gift_offer_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['gift_offer_id'], 'client_gift_offer_ibfk_2')->references(['id'])->on('gift_offers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_gift_offer', function (Blueprint $table) {
            $table->dropForeign('client_gift_offer_ibfk_1');
            $table->dropForeign('client_gift_offer_ibfk_2');
        });
    }
};
