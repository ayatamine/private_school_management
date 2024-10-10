<?php

use App\Models\PaymentMethod;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_category_id');
            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories')->onUpdate('cascade');
            $table->double('value');
            $table->foreignId('payment_method_id')->constrained();
            $table->boolean('is_tax_included')->default(0);
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
        Schema::dropIfExists('expenses');
    }
};
