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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_number')->nullable();
            $table->date('date');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=>pending, 1=>approved, 2=>rejected');
            $table->string('currency')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('salary_id')->nullable();
            $table->bigInteger('purchase_invoice_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable()->index('journal_entries_client_id_foreign');
            $table->unsignedBigInteger('employee_id')->nullable()->index('journal_entries_employee_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('journal_entries_invoice_id_foreign');
            $table->unsignedBigInteger('cost_center_id')->nullable()->index('journal_entries_cost_center_id_foreign');
            $table->unsignedBigInteger('created_by_employee')->nullable()->index('journal_entries_created_by_employee_foreign');
            $table->unsignedBigInteger('approved_by_employee')->nullable()->index('journal_entries_approved_by_employee_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
