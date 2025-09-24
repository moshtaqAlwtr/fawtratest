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
        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('phone');
            $table->tinyInteger('status')->default(0)->comment('0 = active , 1 = inactive ');
            $table->string('mobile');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('country');
            $table->boolean('is_main')->default(false);
            $table->text('work_hours')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
