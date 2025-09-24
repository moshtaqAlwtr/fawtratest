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
        Schema::create('payable_cheques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 15);
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('bank_id')->index('payable_cheques_bank_id_foreign');
            $table->unsignedBigInteger('cheque_book_id')->index('payable_cheques_cheque_book_id_foreign');
            $table->string('cheque_number')->unique();
            $table->unsignedBigInteger('recipient_account_id');
            $table->string('payee_name');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payable_cheques');
    }
};
