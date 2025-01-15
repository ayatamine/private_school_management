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
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['terminated_by']);
            $table->dropColumn('terminated_by');
            $table->dropColumn( 'termination_reason');
            $table->dropColumn( 'termination_date');
            $table->dropColumn( 'termination_document');
        });
        Schema::create('student_terminations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            $table->string(column: 'termination_reason')->nullable();
            $table->date(column: 'termination_date')->nullable();
            $table->date(column: 'termination_document')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->foreign('terminated_by')->references('id')->on('users');
            $table->unsignedBigInteger('terminated_semester_id')->nullable();
            $table->foreign('terminated_semester_id')->references('id')->on('semesters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_terminations');
        Schema::table('students', function (Blueprint $table) {
            $table->string(column: 'termination_reason')->nullable();
            $table->date(column: 'termination_date')->nullable();
            $table->date(column: 'termination_document')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->foreign('terminated_by')->references('id')->on('users');
        });
    }
};
