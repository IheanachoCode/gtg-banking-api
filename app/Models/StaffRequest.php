<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRequest extends Model
{
    protected $table = 'staff_request_table';
    
    protected $fillable = [
        'request_date',
        'request_name',
        'request_no',
        'description',
        'amount',
        'approved_by',
        'status',
        'request_by',
        'posted_by'
    ];
}