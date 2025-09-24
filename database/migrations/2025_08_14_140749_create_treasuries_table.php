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
        Schema::create('treasuries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('type')->comment('(0 خزينه) , (حساب بنكي 1)');
            $table->tinyInteger('status')->default(0)->comment('(0 نشط) , (غير نشط 1)');
            $table->text('description')->nullable();
            $table->tinyInteger('is_main')->default(0)->comment('(0 غير رئيسية) , (رئيسية 1)');
            $table->string('bank_name')->nullable();
            $table->bigInteger('account_number')->nullable();
            $table->tinyInteger('currency')->nullable();
            $table->tinyInteger('deposit_permissions')->default(0)->comment('صلاحيه ايداع | 1-موظف 2-دور وظيفي 3-فرع');
            $table->tinyInteger('withdraw_permissions')->default(0)->comment('صلاحيه سحب | 1-موظف 2-دور وظيفي 3-فرع');
            $table->integer('value_of_deposit_permissions')->nullable();
            $table->integer('value_of_withdraw_permissions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasuries');
    }
};
