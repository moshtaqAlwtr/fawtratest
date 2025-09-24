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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('branch_name')->nullable();
            $table->string('account_holder_name');
            $table->enum('account_status', ['active', 'inactive'])->default('active');
            $table->string('currency', 10);
            $table->text('description')->nullable();
            $table->enum('permissions', ['withdraw', 'deposit', 'both'])->default('both');
            $table->decimal('balance', 15)->default(0);
            $table->unsignedBigInteger('account_id')->nullable()->index('bank_accounts_account_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
