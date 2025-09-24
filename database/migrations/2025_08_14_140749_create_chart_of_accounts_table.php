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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['asset', 'liability', 'income', 'expense']);
            $table->string('operation')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index('chart_of_accounts_parent_id_foreign');
            $table->integer('level')->default(1);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
