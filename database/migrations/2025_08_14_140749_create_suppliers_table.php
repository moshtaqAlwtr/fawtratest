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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number_suply')->unique();
            $table->string('trade_name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,2=inactive');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('street1')->nullable();
            $table->string('street2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('SA');
            $table->string('tax_number')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->decimal('opening_balance', 15)->nullable();
            $table->date('opening_balance_date')->nullable();
            $table->string('currency')->nullable()->default('SAR');
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable()->index('suppliers_employee_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('suppliers_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('suppliers_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
