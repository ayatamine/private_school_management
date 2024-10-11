<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploymentDuration extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_id',
        'designation_id',
        'contract_start_date',
        'contract_end_date',
        'contract_image',
        'note',
        'attachment',
        'contract_end_reason',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'designation_id' => 'integer',
        'department_id' => 'integer',
    ];
    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function designation():BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
