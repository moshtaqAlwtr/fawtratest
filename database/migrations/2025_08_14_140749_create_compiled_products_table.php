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
        Schema::create('compiled_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('compile_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('qyt')->nullable();
            $table->unsignedBigInteger('storehouse_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compiled_products');
    }
};
