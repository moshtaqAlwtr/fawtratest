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
        Schema::create('purchase_quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('code')->unique();
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['disagree', 'approval', 'Under Review'])->nullable()->default('Under Review');
            $table->bigInteger('order_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_quotations_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('purchase_quotations_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotations');
    }
};
