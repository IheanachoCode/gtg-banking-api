<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otp_table';
    
    protected $fillable = ['otp', 'customer_contact', 'status'];
    
    public $timestamps = true;

    const STATUS_UNVERIFIED = 'Unverified';
    const STATUS_VERIFIED = 'Verified';
}