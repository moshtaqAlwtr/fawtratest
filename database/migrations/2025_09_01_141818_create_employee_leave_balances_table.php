<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->year('year'); // السنة المالية
            $table->integer('initial_balance')->default(0); // الرصيد المبدئي
            $table->integer('used_balance')->default(0); // الرصيد المستخدم
            $table->integer('remaining_balance')->default(0); // الرصيد المتبقي
            $table->integer('carried_forward')->default(0); // المرحل من السنة السابقة
            $table->integer('additional_balance')->default(0); // رصيد إضافي (مكافآت)
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();

            // فهارس للأداء
            $table->index(['employee_id', 'year']);
            $table->index(['leave_type_id', 'year']);
            $table->unique(['employee_id', 'leave_type_id', 'year']);

            // Foreign keys

        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
