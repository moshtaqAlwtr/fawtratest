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
        Schema::create('commission__products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('commission_id');
            $table->integer('product_id');
            $table->decimal('commission_percentage', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission__products');
    }
};
