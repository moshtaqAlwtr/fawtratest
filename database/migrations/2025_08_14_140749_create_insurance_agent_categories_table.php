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
        Schema::create('insurance_agent_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->index('insurance_agent_categories_category_id_foreign');
            $table->unsignedBigInteger('insurance_agent_id')->index('insurance_agent_categories_insurance_agent_id_foreign');
            $table->string('name')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('company_copayment')->default(0);
            $table->decimal('client_copayment')->default(0);
            $table->decimal('max_copayment')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('type')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_agent_categories');
    }
};
