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
        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('journal_entry_id')->nullable()->index('journal_entry_details_journal_entry_id_foreign');
            $table->unsignedBigInteger('account_id')->nullable()->index('journal_entry_details_account_id_foreign');
            $table->string('description')->nullable();
            $table->decimal('debit', 15)->default(0);
            $table->decimal('credit', 15)->default(0);
            $table->string('reference')->nullable();
            $table->string('currency')->default('SAR');
            $table->boolean('is_debit')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_details');
    }
};
