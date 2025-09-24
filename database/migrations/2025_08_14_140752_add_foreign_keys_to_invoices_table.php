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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign(['client_id'])->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['treasury_id'])->references(['id'])->on('treasuries')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_client_id_foreign');
            $table->dropForeign('invoices_created_by_foreign');
            $table->dropForeign('invoices_employee_id_foreign');
            $table->dropForeign('invoices_treasury_id_foreign');
            $table->dropForeign('invoices_updated_by_foreign');
        });
    }
};
