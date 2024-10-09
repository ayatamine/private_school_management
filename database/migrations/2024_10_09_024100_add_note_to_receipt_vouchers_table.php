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
        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->string('simple_note')->nullable();
            $table->string('reject_note')->nullable();
            $table->enum( 'status',['pending','paid','rejected'])->default('pending');
            $table->dropColumn('is_approved');
            $table->enum('added_by',['student','parent'])->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->dropColumn('simple_note');
            $table->dropColumn('reject_note');
            $table->dropColumn('status');
            $table->boolean('is_approved')->default(0);
            $table->dropColumn('added_by');
        });
    }
};
