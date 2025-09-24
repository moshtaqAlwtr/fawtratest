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
        Schema::create('installments_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 10)->nullable();
            $table->unsignedBigInteger('installments_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments_details');
    }
};
