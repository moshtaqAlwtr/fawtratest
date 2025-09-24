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
        Schema::create('salary_template', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('status')->nullable()->default(1)->comment('(1=>active)  (2=>not active)');
            $table->tinyInteger('receiving_cycle')->nullable()->default(1)->comment('1=>monthly, 2=>weekly, 3=>yearly , 4=>Quarterly,5=>Once a week');
            $table->decimal('amount', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_template');
    }
};
