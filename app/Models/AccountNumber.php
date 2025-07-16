<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountNumber extends Model
{
    protected $table = 'account_number';
    
    protected $fillable = [
        'account_no',
        'user_id',
        'account_type',
        'account_status',
        'date_created'
    ];

    protected $casts = [
        'date_created' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(ClientRegistration::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(ClientDepositWithdrawal::class, 'account_no', 'account_no');
    }
} 