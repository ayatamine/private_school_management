<?php

namespace App\Models;

use App\Models\User;
use App\Models\ExpenseName;
use App\Models\PaymentMethod;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_category_id',
        'value',
        'payment_method_id',
        'is_tax_included',
        'note',
        'attachment',
        'registered_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_category_id' => 'integer',
        'payment_method_id' => 'integer',
        'registered_by' => 'integer',
        'is_tax_included' => 'boolean',
    ];
    public function transactionCategory():BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class,'transaction_category_id','id')->where('type','expense');
    }
    public function paymentMethod():BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function registeredBy():BelongsTo
    {
        return $this->belongsTo(User::class,'registered_by','id');
    }
}
