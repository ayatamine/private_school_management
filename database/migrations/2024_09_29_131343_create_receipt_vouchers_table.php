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
        Schema::disableForeignKeyConstraints();

        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            $table->float('value');
            $table->string('value_in_alphabetic')->nullable();
            $table->string('document')->nullable();
            $table->boolean('is_approved')->default(0);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_mehtods');
            $table->date('payment_date');
            $table->unsignedBigInteger('registered_by');
            $table->foreign('registered_by')->references('id')->on('users');
            $table->foreignId('user_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_vouchers');
    }
};
