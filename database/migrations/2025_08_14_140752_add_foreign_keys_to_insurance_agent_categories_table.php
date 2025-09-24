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
        Schema::table('insurance_agent_categories', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['insurance_agent_id'])->references(['id'])->on('insurance_agents')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_agent_categories', function (Blueprint $table) {
            $table->dropForeign('insurance_agent_categories_category_id_foreign');
            $table->dropForeign('insurance_agent_categories_insurance_agent_id_foreign');
        });
    }
};
