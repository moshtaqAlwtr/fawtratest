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
        Schema::table('asset_dep', function (Blueprint $table) {
            $table->foreign(['acc_dep_account_id'])->references(['id'])->on('accounts')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['asset_id'])->references(['id'])->on('asset_depreciations')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['dep_account_id'])->references(['id'])->on('accounts')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_dep', function (Blueprint $table) {
            $table->dropForeign('asset_dep_acc_dep_account_id_foreign');
            $table->dropForeign('asset_dep_asset_id_foreign');
            $table->dropForeign('asset_dep_dep_account_id_foreign');
        });
    }
};
