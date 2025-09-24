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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->integer('code')->unique();
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->enum('status', ['approval', 'disagree', 'Convert to Quotation', 'Under Review'])->nullable()->default('Under Review');
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_orders_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('purchase_orders_updated_by_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
