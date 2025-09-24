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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم');
            $table->string('commentable_type')->comment('نوع العنصر (project, task)');
            $table->unsignedBigInteger('commentable_id')->comment('معرف العنصر');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('معرف التعليق الاب');
            $table->longText('content')->comment('محتوى التعليق');
            $table->boolean('is_edited')->default(false)->comment('هل تم تعديل التعليق');
            $table->timestamp('edited_at')->nullable()->comment('تاريخ آخر تعديل');
            $table->timestamps();
            $table->softDeletes()->comment('تاريخ الحذف (Soft Delete)');

            $table->index(['commentable_type', 'commentable_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
