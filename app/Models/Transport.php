<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id',
        'transport_fees_id',
        'registration_date',
        'registred_by',
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
        'transport_fees_id' => 'integer',
        'registration_date' => 'date',
        'registred_by' => 'integer',
        'transport_fee_id' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function transportFee(): BelongsTo
    {
        return $this->belongsTo(TransportFee::class);
    }

    public function transportFees(): BelongsTo
    {
        return $this->belongsTo(TransportFee::class);
    }

    public function registredBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
