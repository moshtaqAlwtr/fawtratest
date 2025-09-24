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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id')->comment('مساحة العمل')->nullable();
            $table->string('title')->comment('اسم المشروع');
            $table->longText('description')->nullable()->comment('وصف المشروع');
            $table->enum('status', ['new', 'in_progress', 'completed', 'on_hold'])->default('new')->comment('حالة المشروع');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('أولوية المشروع');
            $table->decimal('budget', 10, 2)->nullable()->comment('ميزانية المشروع');
            $table->date('start_date')->comment('تاريخ البداية');
            $table->date('end_date')->comment('تاريخ النهاية المتوقعة');
            $table->date('actual_end_date')->nullable()->comment('تاريخ النهاية الفعلية');
            $table->tinyInteger('progress_percentage')->unsigned()->default(0)->comment('نسبة الإنجاز 0-100');
            $table->unsignedBigInteger('created_by')->nullable()->comment('من قبل');
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['priority', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
