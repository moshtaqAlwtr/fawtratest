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
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('commission_name')->nullable();
            $table->tinyInteger('members')->nullable()->default(1);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->decimal('price', 10)->nullable();
            $table->tinyInteger('period')->nullable()->default(1);
            $table->string('duration')->nullable();
            $table->tinyInteger('payment_rate')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
