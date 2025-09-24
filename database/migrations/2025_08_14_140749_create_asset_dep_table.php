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
        Schema::create('asset_dep', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id')->index('asset_dep_asset_id_foreign');
            $table->decimal('salvage_value', 15)->nullable()->comment('قيمة الخردة');
            $table->tinyInteger('dep_method')->nullable()->comment('1=القسط الثابت, 2=القسط المتناقص, 3=وحدات الانتاج, 4=بدون الاهلاك');
            $table->decimal('dep_rate', 15)->nullable()->comment('قيمة/نسبة الإهلاك');
            $table->integer('duration')->nullable()->comment('مدة الإهلاك');
            $table->tinyInteger('period')->nullable()->comment('1=يومي, 2=شهري, 3=سنوي');
            $table->string('unit_name')->nullable()->comment('اسم الوحدة');
            $table->integer('total_units')->nullable()->comment('إجمالي الوحدات');
            $table->decimal('acc_dep', 15)->default(0)->comment('مجمع الإهلاك');
            $table->decimal('book_value', 15)->default(0)->comment('القيمة الدفترية');
            $table->date('last_dep_date')->nullable()->comment('تاريخ آخر إهلاك');
            $table->date('end_date')->nullable()->comment('تاريخ انتهاء الإهلاك');
            $table->unsignedBigInteger('dep_account_id')->nullable()->index('asset_dep_dep_account_id_foreign');
            $table->unsignedBigInteger('acc_dep_account_id')->nullable()->index('asset_dep_acc_dep_account_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_dep');
    }
};
