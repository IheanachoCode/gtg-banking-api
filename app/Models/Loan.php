<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loan_table';
    
    protected $fillable = [
        'account_number',
        'date_issued',
        'client_name',
        'address',
        'loan_type',
        'processing_fee',
        'amount',
        'refence_no',
        'userID',
        'state'
    ];
}