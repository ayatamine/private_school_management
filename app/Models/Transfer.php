<?php

namespace App\Models;

use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_account_id', 'to_account_id', 'amount', 'transfer_date', 'note'
    ];

    public function fromAccount() {
        return $this->belongsTo(FinanceAccount::class, 'from_account_id');
    }

    public function toAccount() {
        return $this->belongsTo(FinanceAccount::class, 'to_account_id');
    }
        /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'from_account_id' => 'integer',
        'to_account_id' => 'integer',
        'amount' => 'double',
        'transfer_date' => 'date',
    ];
}
