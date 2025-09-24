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
        Schema::create('taxs_invoice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('rate', 5);
            $table->string('type')->default('included');
            $table->bigInteger('purchase_quotation_view_id')->nullable();
            $table->decimal('value', 10)->nullable()->default(0);
            $table->timestamps();
            $table->string('type_invoice')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxs_invoice');
    }
};
