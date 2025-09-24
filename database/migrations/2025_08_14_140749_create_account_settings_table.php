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
        Schema::create('account_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('currency')->nullable();
            $table->string('negative_currency_formats')->nullable();
            $table->string('time_formula')->nullable();
            $table->string('timezone')->nullable();
            $table->string('attachments')->nullable();
            $table->string('language')->nullable();
            $table->string('printing_method')->nullable();
            $table->enum('business_type', ['products', 'services', 'both'])->default('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
