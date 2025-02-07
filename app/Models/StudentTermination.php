<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentTermination extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'termination_date',
        'termination_reason',
        'termination_document',
        'terminated_by',
        'terminated_semester_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'student_id' => 'integer',
        'terminated_by' => 'integer',
        'terminated_semester_id' => 'integer',
        'termination_date' => 'date',
    ];
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class,'student_id','id');
    }
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class,'terminated_semester_id','id');
    }
    public function terminatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'terminated_by','id');
    }
}
