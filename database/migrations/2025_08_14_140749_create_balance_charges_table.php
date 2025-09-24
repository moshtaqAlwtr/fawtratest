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
        Schema::create('balance_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('balance_charges_client_id_foreign');
            $table->unsignedBigInteger('balance_type_id')->nullable()->index('balance_charges_balance_type_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('balance_charges_invoice_id_foreign');
            $table->decimal('value', 10)->nullable();
            $table->decimal('remaining', 10)->nullable();
            $table->decimal('consumer', 10)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->boolean('contract_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_charges');
    }
};
