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
        Schema::enableForeignKeyConstraints();
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_category_id')->constrained();
            $table->double('value');
            $table->foreignId('payment_method_id')->constrained();
            $table->mediumText('note')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('registered_by')->references('id')->on('users')->constrained();
            $table->timestamps();
        });
        Schema::disableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
