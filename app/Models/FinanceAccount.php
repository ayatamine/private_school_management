<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'opening_balance',
        'balance',
        'is_active',
        'is_visible',
        'bank_name',
        'account_number',
        'link_with_employee_payments',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'opening_balance' => 'double',
        'balance' => 'double',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
    ];
}
