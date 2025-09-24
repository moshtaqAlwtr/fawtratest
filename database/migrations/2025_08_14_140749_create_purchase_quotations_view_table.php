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
        Schema::create('purchase_quotations_view', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('purchase_price_number')->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->date('date');
            $table->integer('valid_days')->default(0);
            $table->decimal('subtotal', 15)->default(0);
            $table->decimal('total_discount', 15)->default(0);
            $table->enum('status', ['disagree', 'approval', 'Under Review'])->nullable()->default('Under Review');
            $table->bigInteger('tax_id')->nullable();
            $table->decimal('grand_total', 15)->default(0);
            $table->bigInteger('quotation_id')->nullable();
            $table->decimal('shipping_cost', 15)->default(0);
            $table->decimal('discount_amount', 15)->default(0);
            $table->decimal('total_tax', 10, 0)->nullable();
            $table->enum('discount_type', ['amount', 'percentage'])->default('amount');
            $table->string('adjustment_label')->nullable();
            $table->enum('adjustment_type', ['discount', 'addition'])->nullable();
            $table->decimal('adjustment_value', 15)->default(0);
            $table->enum('tax_type', ['vat', 'zero', 'exempt'])->default('vat');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotations_view');
    }
};
