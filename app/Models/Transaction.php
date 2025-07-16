<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'client_deposit_withdrawal';
    
    protected $fillable = [
        'account_no',
        'amount',
        'transaction_id',
        'transaction_type',
        'transact_from',
        'transact_to',
        'payment_mode',
        'transaction_date',
        'created_at'
    ];

    protected $appends = ['description', 'time'];

    public function getDescriptionAttribute()
    {
        return 'FRM ' . $this->transact_from . ' TO ' . $this->transact_to;
    }

    public function getTimeAttribute()
    {
        return explode(" ", $this->created_at)[1] ?? '';
    }


    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

















}