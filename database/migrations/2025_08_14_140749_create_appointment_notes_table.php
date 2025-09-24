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
        Schema::create('appointment_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index('appointment_notes_user_id_foreign');
            $table->unsignedBigInteger('client_id')->index('appointment_notes_client_id_foreign');
            $table->unsignedBigInteger('appointment_id')->index('appointment_notes_appointment_id_foreign');
            $table->string('action_type')->nullable();
            $table->boolean('share_with_client')->default(false);
            $table->date('date');
            $table->time('time');
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_notes');
    }
};
