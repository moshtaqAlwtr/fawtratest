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
        Schema::table('default_warehouses', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['storehouse_id'])->references(['id'])->on('store_houses')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('default_warehouses', function (Blueprint $table) {
            $table->dropForeign('default_warehouses_employee_id_foreign');
            $table->dropForeign('default_warehouses_storehouse_id_foreign');
        });
    }
};
