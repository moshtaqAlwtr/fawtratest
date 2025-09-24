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
        Schema::table('cheque_books', function (Blueprint $table) {
            $table->foreign(['bank_id'])->references(['id'])->on('treasuries')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheque_books', function (Blueprint $table) {
            $table->dropForeign('cheque_books_bank_id_foreign');
        });
    }
};
