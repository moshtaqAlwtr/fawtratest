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
        Schema::create('production_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('product_id')->index('production_materials_product_id_foreign');
            $table->unsignedBigInteger('account_id')->index('production_materials_account_id_foreign');
            $table->unsignedBigInteger('production_path_id')->index('production_materials_production_path_id_foreign');
            $table->decimal('quantity', 10);
            $table->decimal('last_total_cost', 10);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('default')->default(0);
            $table->unsignedBigInteger('created_by')->nullable()->index('production_materials_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('production_materials_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_materials');
    }
};
