<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'finance_account_id',
        'code',
        'is_code_required',
        'is_active_for_students_and_parents',
        'show_in_expenses',
        'show_in_receipt_voucher',
        'show_in_incomes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'finance_account_id' => 'integer',
        'is_code_required' => 'bool',
        'is_active_for_students_and_parents' => 'bool',
        'show_in_expenses' => 'bool',
        'show_in_receipt_voucher' => 'bool',
        'show_in_incomes' => 'bool',
    ];

    public function financeAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class);
    }
}
