<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;

class RequestPayment extends Model
{
    use HasFactory;

   protected $table = 'request_payment_table';
   protected $guarded = ['id'];

   
}