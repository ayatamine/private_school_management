<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateStudentsToStudentSemesters extends Migration
{
    public function up()
    {
        // Get all students with semester_id
        $students = DB::table('students')
            ->whereNotNull('semester_id')
            ->get();

        foreach ($students as $student) {
            // Get the semester and academic year
            $semester = DB::table('semesters')
                ->where('id', $student->semester_id)
                ->first();

            if ($semester) {
                // Create a new student_semester entry
                DB::table('student_semesters')->insert([
                    'student_id' => $student->id,
                    'semester_id' => $semester->id,
                    'enrollment_date' => $student->created_at,
                    'is_current' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Remove the semester_id column from students table
        // Schema::table('students', function (Blueprint $table) {
        //     $table->dropColumn('semester_id');
        // });
    }

    public function down()
    {
        // Add back the semester_id column
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null');
        });

        // Get all current semesters and update students
        $studentSemesters = DB::table('student_semesters')
            ->where('is_current', true)
            ->get();

        foreach ($studentSemesters as $studentSemester) {
            DB::table('students')
                ->where('id', $studentSemester->student_id)
                ->update(['semester_id' => $studentSemester->semester_id]);
        }

        // Drop the student_semesters table
        Schema::dropIfExists('student_semesters');
    }
}
