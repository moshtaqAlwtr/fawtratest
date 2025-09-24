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
        Schema::create('loyalty_sittings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('minimum_import_points', 10)->nullable();
            $table->unsignedBigInteger('client_credit_type_id')->index('loyalty_sittings_client_credit_type_id_foreign');
            $table->decimal('client_loyalty_conversion_factor', 10)->nullable();
            $table->boolean('allow_decimal')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_sittings');
    }
};
