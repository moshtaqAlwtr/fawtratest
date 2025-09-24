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
        Schema::create('receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->unique();
            $table->decimal('amount', 10);
            $table->text('description')->nullable();
            $table->date('date');
            $table->integer('incomes_category_id')->nullable();
            $table->string('seller')->nullable();
            $table->unsignedBigInteger('treasury_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->tinyInteger('is_recurring')->default(0);
            $table->string('recurring_frequency')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('tax1')->nullable();
            $table->tinyInteger('tax2')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->decimal('tax1_amount', 10)->nullable();
            $table->decimal('tax2_amount', 10);
            $table->string('attachments')->nullable();
            $table->tinyInteger('cost_centers_enabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
