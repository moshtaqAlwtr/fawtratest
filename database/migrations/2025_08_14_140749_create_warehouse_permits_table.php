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
        Schema::create('warehouse_permits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('permission_type')->nullable();
            $table->dateTime('permission_date')->nullable();
            $table->tinyInteger('sub_account')->nullable();
            $table->string('number')->nullable();
            $table->integer('store_houses_id')->nullable();
            $table->integer('from_store_houses_id')->nullable();
            $table->integer('to_store_houses_id')->nullable();
            $table->enum('reference_type', ['normal', 'purchase_invoice'])->nullable()->default('normal');
            $table->bigInteger('reference_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', ''])->nullable()->default('pending');
            $table->text('details')->nullable();
            $table->text('attachments')->nullable();
            $table->decimal('grand_total', 10)->nullable();
            $table->unsignedBigInteger('created_by')->index('warehouse_permits_created_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_permits');
    }
};
