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
        Schema::create('client_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('quotation_id')->nullable();
            $table->string('process')->nullable();
            $table->time('time')->nullable();
            $table->integer('notes')->nullable();
            $table->date('date')->nullable();
            $table->integer('deposit_count')->nullable();
            $table->enum('site_type', ['independent_booth', 'grocery', 'supplies', 'markets', 'station'])->default('markets');
            $table->json('additional_data')->nullable();
            $table->integer('competitor_documents')->nullable();
            $table->bigInteger('employee_view_status')->nullable();
            $table->bigInteger('invoice_id')->nullable();
            $table->text('attachments')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->bigInteger('employee_id')->nullable();
            $table->enum('type', ['client', 'invoice', 'quotation', 'purchase_Request', 'purchase_invoice'])->default('client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_relations');
    }
};
