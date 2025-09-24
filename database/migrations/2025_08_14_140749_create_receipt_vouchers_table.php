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
        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('payer_name');
            $table->decimal('amount', 15);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('account_id')->nullable()->index('receipt_vouchers_account_id_foreign');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_vouchers');
    }
};
