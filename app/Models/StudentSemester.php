<?php

namespace App\Models;

use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentSemester extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'semester_id',
        'academic_year_id',
        'enrollment_date',
        'completion_date',
        'is_current',
        'is_promoted',
        'promotion_notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'student_id' => 'integer',
        'semester_id' => 'integer',
        'academic_year_id' => 'integer',
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'is_current' => 'boolean',
        'is_promoted' => 'boolean',
    ];

    /**
     * Get the student that owns the semester.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the academic year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the duration of enrollment in days.
     */
    public function getEnrollmentDurationAttribute()
    {
        if ($this->completion_date) {
            return $this->completion_date->diffInDays($this->enrollment_date);
        }
        return now()->diffInDays($this->enrollment_date);
    }
}
