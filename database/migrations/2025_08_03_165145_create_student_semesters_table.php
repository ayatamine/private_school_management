<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            // $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->boolean('is_current')->default(true);
            $table->boolean('is_promoted')->default(false);
            $table->text('promotion_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_semesters');
    }
};
