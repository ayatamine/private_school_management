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
use App\Models\StudentTermination;
use App\Models\StudentSemester;
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
        'parent_id',
        'approved_at',
        'registered_by',
        'registration_number',
        'user_id',
        'gender',
        'opening_balance',
        'finance_document',
        'note',
        'status',
        'created_at',
        'parent_relation',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'birth_date' => 'date',
        'parent_id' => 'integer',
        'approved_at' => 'timestamp',
        'registered_by' => 'integer',
        'user_id' => 'integer',
        'opening_balance' => 'double',
    ];
    protected $appends=['username','balance','total_fees_after_due_date','total_fees_rest','current_balance','transport_registration_date'];
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
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function username():Attribute
    {
        return Attribute::make(
            get: function ($value) {
             
                return "$this->first_name  $this->last_name" ;
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
                return $this->totalFees()  ;
            }
        );
    }
    public function totalFeesAfterDueDate():Attribute //اجمالي الرسوم بعد تاريخ الاستحقاق
    {
        return Attribute::make(
            get: function ($value) {
                return $this->totalFees(after_due_date:true) ;
            }
        );
    }
    public function totalFeesRest():Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return floatval($this->totalFeesAfterDueDate) -  $this->payments() ;
            }
        );
    }
    public function currentBalance():Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return floatval($this->totalFeesRest) + $this->opening_balance ;
            }
        );
    }
    public function transportRegistrationDate():Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $transport = $this->transport;
                return $transport?->created_at;
            }
        );
    }

    public function termination():HasOne
    {
        return $this->hasOne(StudentTermination::class);
    }

    public function semesters()
    {
        return $this->hasMany(StudentSemester::class)->with('semester','academicYear')
        ->orderBy('enrollment_date','asc');
    }

    public function currentSemester()
    {
        return $this->hasOne(StudentSemester::class)
            ->where('is_current', true)
            ->with('semester');
    }
    public function promotedSemester()
    {
        return $this->hasOne(StudentSemester::class)
            ->where('is_promoted', true)
            ->with('semester');
    }

    public function promoteToNextSemester($newSemesterId, $notes = null)
    {
        // Get current semester
        $currentSemester = $this->currentSemester;
        
        if (!$currentSemester) {
            throw new \Exception('Student has no current semester');
        }

        // Mark current semester as completed
        $currentSemester->update([
            'is_current' => false,
            'completion_date' => now(),
            'is_promoted' => true,
            'promotion_notes' => $notes
        ]);

        // Create new semester entry
        return StudentSemester::create([
            'student_id' => $this->id,
            'semester_id' => $newSemesterId,
            'academic_year_id' => Semester::find($newSemesterId)->academic_year_id,
            'enrollment_date' => now(),
            'is_current' => false,
            'is_promoted' => true,
            'promotion_notes' => $notes
        ]);
    }
}
