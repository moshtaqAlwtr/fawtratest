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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('اسم مساحة العمل');
            $table->text('description')->nullable()->comment('وصف مساحة العمل');
            $table->unsignedBigInteger('admin_id')->comment('مسؤول المساحة');
            $table->boolean('is_primary')->default(false)->comment('هل هي المساحة الرئيسية');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
