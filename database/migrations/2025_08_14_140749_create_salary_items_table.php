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
        Schema::create('salary_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('type')->nullable()->default(1)->comment('(1=>deduction) (2=>addition)');
            $table->tinyInteger('status')->nullable()->default(1)->comment('(1=>active)  (2=>not active)');
            $table->text('description')->nullable();
            $table->tinyInteger('salary_item_value')->nullable()->default(1)->comment('(1=>amount) (2=>calculation_formula)');
            $table->decimal('amount', 10)->nullable();
            $table->string('calculation_formula')->nullable();
            $table->text('condition')->nullable();
            $table->unsignedBigInteger('account_id')->nullable()->index('salary_items_account_id_foreign');
            $table->unsignedBigInteger('salary_template_id')->nullable();
            $table->unsignedBigInteger('salary_slips_id')->nullable();
            $table->unsignedBigInteger('contracts_id')->nullable();
            $table->boolean('reference_value')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_items');
    }
};
