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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->comment('معرف المهمة');
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم');
            $table->timestamp('assigned_at')->useCurrent()->comment('تاريخ التعيين');
            $table->unsignedBigInteger('assigned_by')->nullable()->comment('من قبل');

            $table->unique(['task_id', 'user_id']);
            $table->index(['task_id', 'assigned_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};
