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
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->comment('كود الأصل');
            $table->string('name')->nullable()->comment('اسم الأصل');
            $table->date('date_price')->nullable()->comment('تاريخ الشراء');
            $table->date('date_service')->nullable()->comment('تاريخ بداية الخدمة');
            $table->unsignedBigInteger('account_id')->nullable()->index('asset_depreciations_account_id_foreign');
            $table->string('place')->nullable()->comment('مكان الأصل');
            $table->integer('region_age')->nullable()->comment('العمر الانتاجي');
            $table->integer('quantity')->nullable()->default(1)->comment('كمية الأصل');
            $table->text('description')->nullable()->comment('وصف الأصل');
            $table->decimal('purchase_value', 15)->nullable()->comment('قيمة الشراء');
            $table->tinyInteger('currency')->nullable()->default(1)->comment('1=ريال, 2=دولار');
            $table->string('cash_account')->nullable()->comment('حساب النقدية');
            $table->tinyInteger('tax1')->nullable()->default(1)->comment('1=القيمة المضافة, 2=صفرية, 3=قيمة مضافة');
            $table->tinyInteger('tax2')->nullable()->default(1)->comment('1=القيمة المضافة, 2=صفرية, 3=قيمة مضافة');
            $table->unsignedBigInteger('employee_id')->nullable()->index('asset_depreciations_employee_id_foreign');
            $table->unsignedBigInteger('client_id')->nullable()->index('asset_depreciations_client_id_foreign');
            $table->string('attachments')->nullable()->comment('مسار ملفات المرفقات');
            $table->tinyInteger('status')->default(2)->comment('1: في الخدمة, 2: تم البيع , 3: مهلك');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};
