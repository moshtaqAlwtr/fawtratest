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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->nullable()->index('clients_employee_id_foreign');
            $table->unsignedBigInteger('branch_id')->nullable()->index('clients_branch_id_foreign');
            $table->string('trade_name');
            $table->string('first_name', 100)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();

            $table->string('street1')->nullable();
            $table->string('street2')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('verification_code', 50)->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->comment('Client classification category');
            $table->string('commercial_registration', 100)->nullable();
            $table->integer('credit_limit')->nullable();
            $table->integer('credit_period')->nullable();
            $table->tinyInteger('printing_method')->nullable()->default(1)->comment('1=>printing 2=>email');
            $table->decimal('opening_balance', 10)->nullable();
            $table->date('opening_balance_date')->nullable();
            $table->integer('code')->nullable();
            $table->string('currency', 50)->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->tinyInteger('client_type')->nullable()->default(1)->comment('1=> Regular Client, 2=> VIP Client');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked', 'potential', 'archived'])->nullable()->default('active');
            $table->text('attachments')->nullable();
            $table->string('category')->nullable();
            $table->boolean('force_show')->nullable();
            $table->timestamp('last_note_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
