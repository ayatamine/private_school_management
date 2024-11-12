<?php

namespace App\Models;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function paymentMethods():HasMany
    {
        return $this->hasMany(PaymentMethod::class,'finance_account_id','id');
    }
    public function balance():Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->opening_balance  + $this->totalTransfert() + $this->totalPayment() + $this->totalIncomes() - $this->totalExpenses();
            }
        );
    }
    public function totalTransfert():float
    {
        $transfers_out = Transfer::where('from_account_id',$this->id)->sum('amount');
        $transfers_in = Transfer::where('to_account_id',$this->id)->sum('amount');
        return floatval($transfers_in - $transfers_out);
    }
   
    public function totalPayment():float
    {
        $payment_methods = $this->paymentMethods()->pluck('id');
    
        $receipt_vouchers = ReceiptVoucher::whereIn('payment_method_id',$payment_methods)->sum('value');
        return $receipt_vouchers;
    }
    public function totalIncomes():float
    {
        $payment_methods = $this->paymentMethods()->pluck('id');
    
        $incomes = Income::whereIn('payment_method_id',$payment_methods)->sum('value');
        return $incomes;
    }
    public function totalExpenses():float
    {
        $payment_methods = $this->paymentMethods()->pluck('id');
    
        $expenses = Expense::whereIn('payment_method_id',$payment_methods)->sum('value');
        return $expenses;
    }
}
