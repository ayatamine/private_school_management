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
        Schema::create('employment_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->references('id')->on('departments')->nullable()->constrained();
            $table->foreignId('designation_id')->references('id')->on('designations')->constrained();
            $table->date('contract_start_date');
            $table->date('contract_end_date')->nullable();
            $table->string('contract_image');
            $table->mediumText('note');
            $table->string('attachment');
            $table->string('contract_end_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_durations');
    }
};
