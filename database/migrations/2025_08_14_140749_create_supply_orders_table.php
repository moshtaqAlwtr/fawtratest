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
        Schema::create('supply_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('order_number')->nullable()->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('client_id')->nullable()->index('supply_orders_client_id_foreign');
            $table->unsignedBigInteger('employee_id')->nullable()->index('supply_orders_employee_id_foreign');
            $table->text('product_details')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipping_policy_file')->nullable();
            $table->string('tag')->nullable();
            $table->decimal('budget', 15)->nullable();
            $table->tinyInteger('currency')->nullable()->default(1)->comment('1: SAR, 2: USD, 3: EUR, 4: GBP, 5: CNY');
            $table->json('custom_fields')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('show_employee')->default(false);
            $table->tinyInteger('status')->nullable()->default(1)->comment('1: Pending, 2: In Progress, 3: Completed, 4: Cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_orders');
    }
};
