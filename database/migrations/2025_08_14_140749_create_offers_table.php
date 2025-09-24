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
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment('اسم العرض');
            $table->date('valid_from')->nullable()->comment('تاريخ بداية العرض');
            $table->date('valid_to')->nullable()->comment('تاريخ نهاية العرض');
            $table->tinyInteger('type')->nullable()->default(1)->comment('نوع العرض: 1 = خصم على الكمية, 2 = خصم على التصنيف');
            $table->decimal('quantity', 10)->nullable()->comment('الكمية المطلوبة لتطبيق العرض');
            $table->tinyInteger('discount_type')->nullable()->default(1)->comment('نوع الخصم: 1 = خصم حقيقي, 2 = خصم نسبي');
            $table->decimal('discount_value', 10)->nullable()->comment('قيمة الخصم');
            $table->string('category')->nullable()->comment('التصنيف');
            $table->unsignedBigInteger('client_id')->nullable()->index('offers_client_id_foreign');
            $table->boolean('is_active')->default(true)->comment('حالة العرض: 1 = نشط, 0 = غير نشط');
            $table->tinyInteger('unit_type')->nullable()->default(1)->comment('نوع الوحدة: 1 = منتج, 2 = تصنيف');
            $table->unsignedBigInteger('product_id')->nullable()->index('offers_product_id_foreign');
            $table->unsignedBigInteger('category_id')->nullable()->index('offers_category_id_foreign');
            $table->tinyInteger('status')->nullable()->default(1)->comment('1=active , 2=not active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
