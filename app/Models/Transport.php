<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'vehicle_id',
        'registered_by',
        'transport_fee_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'vehicle_id' => 'integer',
        'student_id' => 'integer',
        'registered_by' => 'integer',
        'transport_fee_id' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function transportFee(): BelongsTo
    {
        return $this->belongsTo(TransportFee::class);
    }



    public function registredBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'registered_by','id');
    }
}
