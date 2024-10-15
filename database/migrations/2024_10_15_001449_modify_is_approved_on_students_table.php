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
            $table->enum('status',['pending','approved','rejected'])->default('pending');
            $table->dropColumn('is_approved');
            $table->dropForeign('students_course_id_foreign');
            $table->dropColumn('course_id');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->constrained();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->boolean('is_approved');
            $table->foreignId('course_id')->nullable()->references('id')->on('courses')->constrained();
            $table->dropForeign('students_semester_id_foreign');
            $table->dropColumn('semester_id');
        });
    }
};
