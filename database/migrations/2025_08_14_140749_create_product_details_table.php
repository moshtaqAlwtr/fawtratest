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
        Schema::create('product_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10)->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->tinyInteger('type_of_operation')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->text('comments')->nullable();
            $table->string('attachments')->nullable();
            $table->string('subaccount')->nullable();
            $table->unsignedBigInteger('product_id')->index('product_details_product_id_foreign');
            $table->integer('purchase_order_id')->nullable();
            $table->integer('purchase_quotation_id')->nullable();
            $table->integer('duration')->nullable();
            $table->unsignedBigInteger('store_house_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
