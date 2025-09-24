<?php
// database/migrations/2025_09_02_000002_create_attendance_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');

            // التاريخ
            $table->date('attendance_date');

            // الحالة
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');

            // مواعيد الوردية
            $table->time('start_shift')->nullable();
            $table->time('end_shift')->nullable();

            // أوقات الدخول والخروج
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->time('login_time')->nullable();
            $table->time('logout_time')->nullable();

            // أسباب الغياب
            $table->unsignedTinyInteger('absence_type')->nullable();
            $table->integer('absence_balance')->nullable();

            // طريقة تسجيل الدخول والخروج
            $table->enum('check_in_method', ['manual', 'barcode', 'qr'])->default('manual');
            $table->enum('check_out_method', ['manual', 'barcode', 'qr'])->default('manual');
            $table->boolean('scanned_via_barcode')->default(false);

            // بيانات إضافية
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // العلاقات والفهارس

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
