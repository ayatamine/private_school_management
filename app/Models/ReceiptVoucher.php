<?php

namespace App\Models;

use App\Models\User;
use App\Models\Student;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptVoucher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'value',
        'value_in_alphabetic',
        'document',
        'payment_method_id',
        'payment_date',
        'registered_by',
        'simple_note',
        'reject_note',
        'status',
        'refrence_number',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'student_id' => 'integer',
        'value' => 'float',
        'is_approved' => 'boolean',
        'payment_method_id' => 'integer',
        'payment_date' => 'date',
        'registered_by' => 'integer',
    ];
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->where('termination_date',null)->where('status','approved');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class)->withDefault(['name'=>'transfer']);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'registered_by','id');
    }
  

}
