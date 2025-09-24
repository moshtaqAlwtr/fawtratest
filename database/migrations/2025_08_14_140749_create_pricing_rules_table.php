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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pricingName');
            $table->tinyInteger('status')->nullable()->default(1)->comment('1 = active , 2 = inactive');
            $table->string('currency')->default('SAR');
            $table->integer('pricingMethod');
            $table->decimal('dailyPrice', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
