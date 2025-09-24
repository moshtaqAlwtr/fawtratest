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
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();

            // إضافة الأعمدة الأساسية
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->nullable();
            // إضافة الأعمدة المطلوبة للدعوات
            $table->string('email')->nullable()->comment('البريد الإلكتروني للدعوات (للمستخدمين غير المسجلين)');
            $table->string('invite_token', 64)->nullable()->unique()->comment('رمز الدعوة');
            $table->enum('status', ['active', 'pending', 'declined', 'expired'])->default('active')->comment('حالة العضوية/الدعوة');
            $table->timestamp('invited_at')->nullable()->comment('تاريخ إرسال الدعوة');
            $table->timestamp('expires_at')->nullable()->comment('تاريخ انتهاء الدعوة');
  $table->enum('role', ['manager', 'member', 'viewer'])->default('member')->comment('دور المستخدم في المشروع');
            $table->unsignedBigInteger('invited_by')->nullable()->comment('من أرسل الدعوة');
            $table->text('invite_message')->nullable()->comment('رسالة الدعوة الاختيارية');

            $table->timestamps();

            // مفاتيح وفهارس
            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
string:
