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
        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('status');
            $table->enum('period', ['yearly', 'quarterly', 'monthly'])->default('monthly');
            $table->enum('commission_calculation', ['fully_paid', 'partially_paid'])->default('fully_paid');
            $table->timestamps();
            $table->enum('target_type', ['amount', 'quantity'])->default('amount');
            $table->string('value')->nullable();
            $table->string('currency');
            $table->string('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
