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
        Schema::create('manufactur_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedBigInteger('account_id')->index('manufactur_orders_account_id_foreign');
            $table->unsignedBigInteger('employee_id')->index('manufactur_orders_employee_id_foreign');
            $table->unsignedBigInteger('client_id')->index('manufactur_orders_client_id_foreign');
            $table->unsignedBigInteger('product_id')->index('manufactur_orders_product_id_foreign');
            $table->integer('quantity');
$table->enum('status', ['active', 'in_progress', 'completed', 'cancelled'])->default('active');
$table->timestamp('finished_at')->nullable();
$table->unsignedBigInteger('main_warehouse_id')->nullable();
$table->unsignedBigInteger('waste_warehouse_id')->nullable();
$table->decimal('actual_quantity', 10, 2)->nullable();
$table->text('finish_notes')->nullable();
            $table->unsignedBigInteger('production_material_id')->index('manufactur_orders_production_material_id_foreign');
            $table->unsignedBigInteger('production_path_id')->index('manufactur_orders_production_path_id_foreign');
            $table->decimal('last_total_cost', 10);
            $table->unsignedBigInteger('created_by')->nullable()->index('manufactur_orders_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('manufactur_orders_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufactur_orders');
    }
};
