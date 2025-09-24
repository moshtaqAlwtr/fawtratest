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
        Schema::create('leave_type_leave_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_policy_id')->index('leave_type_leave_types_leave_policy_id_foreign');
            $table->unsignedBigInteger('leave_type_id')->index('leave_type_leave_types_leave_type_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_type_leave_types');
    }
};
