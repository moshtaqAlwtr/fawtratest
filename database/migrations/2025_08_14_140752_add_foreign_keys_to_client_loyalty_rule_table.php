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
        Schema::table('client_loyalty_rule', function (Blueprint $table) {
            $table->foreign(['client_id'])->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['loyalty_rule_id'])->references(['id'])->on('loyalty_rules')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_loyalty_rule', function (Blueprint $table) {
            $table->dropForeign('client_loyalty_rule_client_id_foreign');
            $table->dropForeign('client_loyalty_rule_loyalty_rule_id_foreign');
        });
    }
};
