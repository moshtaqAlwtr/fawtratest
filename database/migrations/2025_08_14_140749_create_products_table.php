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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sub_unit_id')->nullable();
            $table->unsignedBigInteger('category_id')->index('products_category_id_foreign');
            $table->string('serial_number')->nullable();
            $table->string('brand', 100)->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('low_stock_thershold')->default(0);
            $table->string('barcode')->nullable();
            $table->string('sales_cost_account')->nullable();
            $table->string('sales_account')->nullable();
            $table->tinyInteger('available_online')->default(0);
            $table->tinyInteger('featured_product')->nullable();
            $table->tinyInteger('track_inventory')->nullable();
            $table->string('inventory_type')->nullable();
            $table->tinyInteger('low_stock_alert')->nullable()->comment('(1=>الكميه) (2=>رقم الشخنه) (3=>تاريخ الانتهاء) (4_رقم الشحنه و تاريخ الانتهاء)');
            $table->text('Internal_notes')->nullable();
            $table->string('tags')->nullable();
            $table->string('images')->nullable();
            $table->tinyInteger('status')->default(1)->comment('(1=>active) (2=>stopped) (3=>not active)');
            $table->decimal('purchase_price', 10)->nullable();
            $table->decimal('sale_price', 10)->nullable();
            $table->integer('purchase_unit_id')->nullable();
            $table->integer('sales_unit_id')->nullable();
            $table->tinyInteger('tax1')->nullable();
            $table->tinyInteger('tax2')->nullable();
            $table->decimal('min_sale_price', 10)->nullable();
            $table->decimal('discount', 10)->nullable();
            $table->tinyInteger('discount_type')->nullable()->comment('(1_percentage) (2_currency)');
            $table->enum('type', ['products', 'services', 'compiled'])->default('products');
            $table->decimal('profit_margin', 10)->nullable();
            $table->unsignedBigInteger('storehouse_id')->nullable()->comment('معرف المخزن الذي يتم تخزين المنتج فيه');
            $table->enum('compile_type', ['Instant', 'Pre-made'])->default('Instant')->comment('نوع التجميع: فوري أو معد مسبقا');
            $table->date('expiry_date')->nullable();
            $table->integer('notify_before_days')->nullable();
            $table->integer('qyt_compile')->nullable()->comment('كمية المنتج المعد مسبقًا (إذا وجدت)');
            $table->unsignedBigInteger('parent_id')->nullable()->index('products_parent_id_foreign');
            $table->bigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
