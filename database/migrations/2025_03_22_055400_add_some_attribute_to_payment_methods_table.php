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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->boolean('show_in_expenses')->default(true);
            $table->boolean('show_in_receipt_voucher')->default(true);
            $table->boolean('show_in_incomes')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('show_in_expenses');
            $table->dropColumn('show_in_receipt_voucher');
            $table->dropColumn('show_in_incomes');
        });
    }
};
