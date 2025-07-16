<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'client_deposit_withdrawal';
    
    protected $fillable = [
        'account_no',
        'amount',
        'transaction_type',
        'transaction_id',
        'transaction_date',
        'user_id',
        'payment_mode'
    ];
}