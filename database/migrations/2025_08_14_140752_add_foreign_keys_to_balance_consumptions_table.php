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
        Schema::table('balance_consumptions', function (Blueprint $table) {
            $table->foreign(['balance_type_id'])->references(['id'])->on('balance_types')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['client_id'])->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_consumptions', function (Blueprint $table) {
            $table->dropForeign('balance_consumptions_balance_type_id_foreign');
            $table->dropForeign('balance_consumptions_client_id_foreign');
            $table->dropForeign('balance_consumptions_invoice_id_foreign');
        });
    }
};
