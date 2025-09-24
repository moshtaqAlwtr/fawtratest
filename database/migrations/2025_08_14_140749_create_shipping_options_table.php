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
        Schema::create('shipping_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('tax')->nullable()->default(1)->comment('الضرائب (1: القيمة المضافة، 2: القيمة الصفرية)');
            $table->decimal('cost', 10)->nullable();
            $table->integer('display_order')->nullable();
            $table->unsignedBigInteger('default_account_id')->nullable()->index('shipping_options_default_account_id_foreign');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_options');
    }
};
