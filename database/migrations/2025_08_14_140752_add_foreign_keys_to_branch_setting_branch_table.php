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
        Schema::table('branch_setting_branch', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['branch_setting_id'])->references(['id'])->on('branch_settings')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_setting_branch', function (Blueprint $table) {
            $table->dropForeign('branch_setting_branch_branch_id_foreign');
            $table->dropForeign('branch_setting_branch_branch_setting_id_foreign');
        });
    }
};
