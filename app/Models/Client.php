<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'client_registrations';
    
    protected $fillable = [
        'user_id',
        'lastname',
        'othernames',
        'password',
        'device_series_no',
        'secret_question',
        'secret_answer',
        'sms_notification',
        'email_notification'
    ];

    protected $hidden = [
        'password',
        'secret_answer'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id', 'user_id');
    }
}