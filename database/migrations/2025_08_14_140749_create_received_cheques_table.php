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
        Schema::create('received_cheques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 15);
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('cheque_number')->unique();
            $table->unsignedBigInteger('recipient_account_id');
            $table->unsignedBigInteger('collection_account_id');
            $table->string('payee_name');
            $table->tinyInteger('endorsement')->default(0)->comment('تظهير');
            $table->string('name')->nullable()->comment('الاسم على ظهر الشيك');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_cheques');
    }
};
