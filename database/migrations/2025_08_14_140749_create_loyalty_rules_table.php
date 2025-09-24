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
        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('priority_level')->nullable();
            $table->decimal('collection_factor', 10, 3)->nullable();
            $table->decimal('minimum_total_spent', 10)->nullable();
            $table->tinyInteger('currency_type')->nullable()->default(1);
            $table->integer('period')->nullable();
            $table->tinyInteger('period_unit')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_rules');
    }
};
