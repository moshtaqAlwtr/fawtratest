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
        Schema::table('jop_titles', function (Blueprint $table) {
            $table->foreign(['department_id'])->references(['id'])->on('departments')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jop_titles', function (Blueprint $table) {
            $table->dropForeign('jop_titles_department_id_foreign');
        });
    }
};
