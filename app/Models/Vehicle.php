<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'car_name',
        'plate_number',
        'form_number',
        'expire_date',
        'insurance_name',
        'insurance_expire_at',
        'periodic_inspection_expire_at',
        'documents',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'expire_date' => 'date',
        'insurance_expire_at' => 'date',
        'periodic_inspection_expire_at' => 'date',
        'documents' => 'array',
    ];
}
