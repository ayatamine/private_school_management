<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Semester;
use App\Models\Transport;
use App\Models\GeneralFee;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use App\Traits\HasPayments;
use App\Models\TransportFee;
use App\Models\ReceiptVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory,HasPayments;

    public static function booted()
    {
        parent::booted();
        // Will fire every time an User is created
        static::created(function (Student $student) {
           if(!$student->registration_number) $student->registration_number = $student->id;
           $student->save();
            //register fees 
            // $tuitionFee = TuitionFee::whereCourseId($student?->semester?->course_id)->first();
            // if($tuitionFee)
            // {
            //   $student->tuitionFees()->attach($tuitionFee->id);
            // }
            //create invoice for student
            // $academic_year_id = $student?->semester?->academic_year_id;
            // $invoice  = Invoice::whereStudentId($student->id)->whereAcademicYearId($academic_year_id)->first();
            // if(!$invoice)
            // {
            //     $invoice =Invoice::create([
            //         'number'=>$student->semester?->academicYear?->name."".$student->registration_number,
            //         'name' => trans('main.fees_invoice')." ".$student?->semester?->academicYear?->name,
            //         'student_id'=>$student->id,
            //         'academic_year_id'=>$academic_year_id,
            //     ]);
            //      $student->invoices()->save($invoice);
            // }
            
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
        'semester_id',
        'parent_id',
        // 'is_approved',
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
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'birth_date' => 'date',
        'semester_id' => 'integer',
        'parent_id' => 'integer',
        'approved_at' => 'timestamp',
        'registered_by' => 'integer',
        'user_id' => 'integer',
        'terminated_by' => 'integer',
        'opening_balance' => 'double',
    ];
    protected $appends=['username','balance'];
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class,'parent_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function transport(): HasOne
    {
        return $this->hasOne(Transport::class);
    }
   

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'registered_by','id');
    }
    public function terminatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'terminated_by','id');
    }
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
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


    public function transportFees()
    {
        return $this->morphedByMany(TransportFee::class, 'feeable', 'student_fee')
                    ->withPivot('discounts', 'created_at')
                    ->withTimestamps();
    }

    public function otherFees()
    {
        return $this->morphedByMany(GeneralFee::class, 'feeable', 'student_fee')
                    ->withPivot('discounts', 'created_at')
                    ->withTimestamps();
    }

    public function tuitionFees()
    {
        return $this->morphedByMany(TuitionFee::class, 'feeable', 'student_fee')
                    ->withPivot('discounts', 'created_at')
                    ->with('academicYear')
                    ->withTimestamps();
    }
    public function receiptVoucher():HasMany
    {
        return $this->hasMany(ReceiptVoucher::class)->where('status','paid');
    }
    public function balance():Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->totalFees() + $this->opening_balance - $this->payments()  ." " .trans("main.".env('DEFAULT_CURRENCY')."");
            }
        );
    }
}
