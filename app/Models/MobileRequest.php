<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileRequest extends Model
{
    protected $table = 'mobile_request';

    protected $fillable = [
        'item_name',
        'tem_code',
        'description',
        'account_no',
        'quantity',
        'price',
        'total',
        'status'
    ];

    public $timestamps = true;
}
