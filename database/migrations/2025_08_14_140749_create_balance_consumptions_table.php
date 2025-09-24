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
        Schema::create('balance_consumptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('balance_consumptions_client_id_foreign');
            $table->unsignedBigInteger('balance_type_id')->nullable()->index('balance_consumptions_balance_type_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('balance_consumptions_invoice_id_foreign');
            $table->date('consumption_date')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->decimal('used_balance', 10)->nullable();
            $table->text('description')->nullable();
            $table->string('contract_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_consumptions');
    }
};
