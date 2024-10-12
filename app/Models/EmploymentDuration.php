<?php

namespace App\Models;

use App\Models\Employee;
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
        'employee_id',
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
        'employee_id' => 'integer',
        'designation_id' => 'integer',
        'department_id' => 'integer',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];
    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function designation():BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
