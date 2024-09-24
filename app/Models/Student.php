<?php

namespace App\Models;

use App\Models\ParentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    public static function booted()
    {
        // Will fire every time an User is created
        static::created(function (Student $student) {
           if(!$student->registration_number) $student->registration_number = $student->id;
           $student->save();
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'third_name',
        'last_name',
        'birth_date',
        'nationality',
        'email',
        'course_id',
        'parent_id',
        'is_approved',
        'approved_at',
        'registered_by',
        'registration_number',
        'user_id',
        'gender',
        'opening_balance',
        'finance_document',
        'note',
        'termination_date',
        'termination_reason',
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
        'birth_date' => 'date',
        'course_id' => 'integer',
        'parent_id' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'timestamp',
        'registered_by' => 'integer',
        'user_id' => 'integer',
        'terminated_by' => 'integer',
        'opening_balance' => 'double',
    ];
    protected $appends=['username'];
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class,'parent_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'registered_by','id');
    }
    public function terminatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'terminated_by','id');
    }
    public function financeDocument():Attribute
    {
        return Attribute::make(
            get: function ($value) {
             
                return $value ? asset("storage/$value") :"";
            }
        );
    }
    public function username():Attribute
    {
        return Attribute::make(
            get: function ($value) {
             
                return "$this->first_name  $this->middle_name" ;
            }
        );
    }
}
