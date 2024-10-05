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
        Schema::table('transports', function (Blueprint $table) {
            $table->string(column: 'termination_reason')->nullable();
            $table->date(column: 'termination_date')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->foreign('terminated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('termination_reason');
            $table->dropColumn('termination_date');
            $table->dropForeign('transports_terminated_by_foreign');
            $table->dropColumn('terminated_by');
        });
    }
};
