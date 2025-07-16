<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class ClientRegistration extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'client_registrations';

    protected $fillable = [
        'user_id',
        'password',
        'lastname',
        'othernames',
        'gender',
        'Nationality',
        'birthday',
        'occupation',
        'phone',
        'email',
        'residential_address',
        'residential_state',
        'Residential_LGA',
        'state_of_origin',
        'LGA_of_origin',
        'town_of_origin',
        'BVN',
        'marital_status',
        'account_type',
        'means_of_identification',
        'identification_no',
        'staff_id',
        'account_officer',
        'next_of_kin_name',
        'next_of_kin_othernames',
        'next_of_kin_address',
        'Relationship_with_Next_of_kin',
        'sms_notification',
        'email_notification',
        'birthday_reminder',
        'Regdate',
        'verification_status',
        'account_status',
        'Pin',
        'email_verified_at',
        'office_address',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'Regdate' => 'date',
    ];

    public function accounts()
    {
        return $this->hasMany(AccountNumber::class, 'user_id', 'user_id');
    }

    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }
} 