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
        Schema::create('income', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('source');
            $table->decimal('amount', 15);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('account_id')->index('income_account_id_foreign');
            $table->unsignedBigInteger('treasury_id')->nullable()->index('income_treasury_id_foreign');
            $table->unsignedBigInteger('bank_account_id')->nullable()->index('income_bank_account_id_foreign');
            $table->unsignedBigInteger('journal_entry_id')->nullable()->index('income_journal_entry_id_foreign');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income');
    }
};
