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
        Schema::create('balance_type_package', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('balance_type_id')->index('balance_type_package_balance_type_id_foreign');
            $table->unsignedBigInteger('package_id')->index('balance_type_package_package_id_foreign');
            $table->decimal('balance_value', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_type_package');
    }
};
