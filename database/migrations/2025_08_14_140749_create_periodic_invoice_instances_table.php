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
        Schema::create('periodic_invoice_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('periodic_invoice_id')->nullable()->index('periodic_invoice_instances_periodic_invoice_id_foreign');
            $table->unsignedBigInteger('invoice_id')->index('periodic_invoice_instances_invoice_id_foreign');
            $table->integer('instance_number');
            $table->date('due_date');
            $table->tinyInteger('status')->default(1)->comment('1=>pending, 2=>generated, 3=>cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_invoice_instances');
    }
};
