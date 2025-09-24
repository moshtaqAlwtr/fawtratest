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
        Schema::table('appointment_notes', function (Blueprint $table) {
            $table->foreign(['appointment_id'])->references(['id'])->on('appointments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['client_id'])->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_notes', function (Blueprint $table) {
            $table->dropForeign('appointment_notes_appointment_id_foreign');
            $table->dropForeign('appointment_notes_client_id_foreign');
            $table->dropForeign('appointment_notes_user_id_foreign');
        });
    }
};
