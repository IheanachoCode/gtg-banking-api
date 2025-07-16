<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $table = 'paymentMode';

    protected $fillable = ['pay_mode'];
}
