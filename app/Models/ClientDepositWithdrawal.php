<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDepositWithdrawal extends Model
{
    protected $table = 'client_deposit_withdrawal';
    
    protected $fillable = [
        'account_no',
        'amount',
        'transaction_type',
        'description',
        'transaction_reference',
        'bill_reference',
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(AccountNumber::class, 'account_no', 'account_no');
    }
} 