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
        Schema::create('gift_offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('target_product_id')->nullable()->index('target_product_id');
            $table->integer('min_quantity')->nullable()->default(1);
            $table->unsignedBigInteger('gift_product_id')->nullable()->index('gift_product_id');
            $table->integer('gift_quantity')->nullable()->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_for_all_clients')->nullable()->default(true);
            $table->boolean('is_for_all_employees')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_offers');
    }
};
