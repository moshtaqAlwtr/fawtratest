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
        Schema::table('balance_type_package', function (Blueprint $table) {
            $table->foreign(['balance_type_id'])->references(['id'])->on('balance_types')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['package_id'])->references(['id'])->on('packages')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_type_package', function (Blueprint $table) {
            $table->dropForeign('balance_type_package_balance_type_id_foreign');
            $table->dropForeign('balance_type_package_package_id_foreign');
        });
    }
};
