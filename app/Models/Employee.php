<?php

namespace App\Models;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'user_id',
        'first_name',
        'middle_name',
        'third_name',
        'last_name',
        // 'department_id',
        // 'designation_id',
        'gender',
        'joining_date',
        'nationality',
        'identity_type',
        'identity_expire_date',
        //new attr
        'birth_date',
        'social_status',
        'study_degree',
        'study_speciality',
        'national_address',
        'iban',
        'documents',
        'termination_reason',
        'termination_date',
        'termination_document',
        'terminated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        // 'department_id' => 'integer',
        // 'designation_id' => 'integer',
        'joining_date' => 'date',
        'identiry_expire_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date',
        'terminated_by' => 'integer',
        'documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function employmentDuration(): HasMany
    {
        return $this->hasMany(employmentDuration::class)->whereNull('contract_end_date');
    }
    // public function department(): BelongsTo
    // {
    //     return $this->belongsTo(Department::class);
    // }

    // public function designation(): BelongsTo
    // {
    //     return $this->belongsTo(Designation::class)->whereIsActive(true);
    // }
}
