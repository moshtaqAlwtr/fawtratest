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
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('code')->nullable()->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('type', ['main', 'sub']);
            $table->enum('balance_type', ['debit', 'credit']);
            $table->decimal('balance', 15)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->integer('deposit_permissions')->nullable();
            $table->integer('withdraw_permissions')->nullable();
            $table->integer('value_of_deposit_permissions')->nullable();
            $table->integer('value_of_withdraw_permissions')->nullable();
            $table->string('type_accont')->nullable();
            $table->bigInteger('supplier_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
