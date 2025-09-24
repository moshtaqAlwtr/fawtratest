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
        Schema::table('income', function (Blueprint $table) {
            $table->foreign(['account_id'])->references(['id'])->on('chart_of_accounts')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['bank_account_id'])->references(['id'])->on('bank_accounts')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['journal_entry_id'])->references(['id'])->on('journal_entries')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['treasury_id'])->references(['id'])->on('treasuries')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income', function (Blueprint $table) {
            $table->dropForeign('income_account_id_foreign');
            $table->dropForeign('income_bank_account_id_foreign');
            $table->dropForeign('income_journal_entry_id_foreign');
            $table->dropForeign('income_treasury_id_foreign');
        });
    }
};
