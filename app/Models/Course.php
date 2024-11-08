<?php

namespace App\Models;

use App\Models\AcademicYear;
use App\Models\AcademicStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_year_id',
        'name',
        'academic_stage_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'academic_year_id' => 'integer',
        'academic_stage_id' => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicStage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class);
    }

}
