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
        Schema::create('credit_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('credit_notifications_client_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('credit_notifications_created_by_foreign');
            $table->date('credit_date')->nullable();
            $table->date('release_date')->nullable();
            $table->string('credit_number')->nullable();
            $table->decimal('subtotal', 10)->nullable();
            $table->decimal('due_value', 10)->nullable();
            $table->tinyInteger('status')->nullable()->default(1)->comment('1:Draft, 2:Pending, 3:Approved, 4:Converted to Invoice, 5:Cancelled');
            $table->decimal('total_discount', 10)->nullable();
            $table->decimal('total_tax', 10)->nullable();
            $table->decimal('shipping_cost', 10)->nullable();
            $table->decimal('next_payment', 10)->nullable();
            $table->decimal('grand_total', 10)->nullable();
            $table->text('notes')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 10)->nullable();
            $table->string('tax_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notifications');
    }
};
