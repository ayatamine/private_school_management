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

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('third_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('email')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('parents');
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('registered_by');
            $table->foreign('registered_by')->references('id')->on('users');
            $table->string('registration_number')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
         
            $table->enum('gender', ["male","female"]);
            $table->double('opening_balance')->default(0);
            $table->string('finance_document')->nullable();
            $table->string('note')->nullable();
            $table->string(column: 'termination_reason')->nullable();
            $table->date(column: 'termination_date')->nullable();
            $table->date(column: 'termination_document')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->foreign('terminated_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
