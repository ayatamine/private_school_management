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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('code')->nullable()->change();
            $table->string('middle_name')->nullable()->change();
            $table->date('joining_date')->nullable()->change();
            $table->string('nationality')->nullable()->change();
            $table->string('identity_type')->nullable()->change();
            $table->date('identity_expire_date')->nullable()->change();
            $table->dropColumn('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('gender')->nullable()->change();
        });
    }
};
