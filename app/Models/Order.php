<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'request_table';
    
    protected $fillable = [
        'request_id',
        'account_id',
        'item_id',
        'item_name',
        'quantity',
        'unit_price',
        'amount_paid',
        'amount_expected',
        'status',
        'delivery'
    ];
}