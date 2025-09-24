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
        Schema::create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->dateTime('visit_date');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->decimal('employee_latitude', 10, 8)->nullable();
            $table->decimal('employee_longitude', 11, 8)->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('client_latitude', 10, 8)->nullable();
            $table->decimal('client_longitude', 11, 8)->nullable();
            $table->decimal('distance')->nullable();
            $table->enum('recording_method', ['auto', 'manual'])->nullable()->default('auto');
            $table->boolean('is_approved')->nullable()->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
