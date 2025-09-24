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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->comment('مشروع المهمة')->nullable();
            $table->unsignedBigInteger('parent_task_id')->nullable()->comment('مهمة الاب');
            $table->string('title')->comment('اسم المهمة');
            $table->longText('description')->nullable()->comment('وصف المهمة');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'overdue'])->default('not_started')->comment('حالة المهمة');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('أولوية المهمة');
            $table->date('start_date')->comment('تاريخ البداية');
            $table->date('due_date')->comment('تاريخ الاستحقاق');
            $table->decimal('budget', 10, 2)->nullable()->comment('ميزانية المهمة');
            $table->date('completed_date')->nullable()->comment('تاريخ الإنجاز الفعلي');
            $table->decimal('estimated_hours', 5, 2)->nullable()->comment('الساعات المقدرة');
            $table->decimal('actual_hours', 5, 2)->nullable()->comment('الساعات الفعلية');
            $table->tinyInteger('completion_percentage')->unsigned()->default(0)->comment('نسبة الإنجاز 0-100');
            $table->json('files')->nullable()->comment('ملفات المهمة - اسم الملف والامتداد');
            $table->unsignedBigInteger('created_by')->nullable()->comment('من قبل');
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['priority', 'due_date']);
            $table->index('created_by');
            $table->index('parent_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
