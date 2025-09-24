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
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم قاعدة الحضور');
            $table->string('color', 7)->default('#4e5381')->comment('لون القاعدة');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('حالة القاعدة');
            $table->unsignedBigInteger('shift_id')->comment('معرف الوردية');
            $table->text('description')->nullable()->comment('وصف القاعدة');
            $table->text('formula')->nullable()->comment('الصيغة الحسابية');
            $table->text('condition')->nullable()->comment('الشرط');
            $table->timestamps();


            // Indexes
            $table->index('status');
            $table->index('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};