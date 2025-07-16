<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Customer extends Model
{
    protected $table = 'client_registrations';
    
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            $customer->user_id = substr($customer->othernames, 0, 5) . rand(1, 900000000);
            $customer->password = Hash::make($customer->user_id);
            $customer->Pin = '0000';
            $customer->verification_status = 'Unverified';
            $customer->account_status = 'Unverified';
            $customer->account_officer = 'No Account Officer';
            $customer->birthday_reminder = date('m-d', strtotime($customer->birthday));
            $customer->Regdate = now()->format('Y-m-d');
        });
    }
}