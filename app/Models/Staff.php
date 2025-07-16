<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Staff extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'staff';
    // protected $primaryKey = 'id';
    protected $primaryKey = 'staffID';

     protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [
        'staffID',
        'email',
        'password',
        'name',
        'Pin',
        'Lastname',
        'othername'

    ];

    protected $hidden = [
        'password',
        'Pin',
    ];
    protected $appends = ['name'];

     public function getFullNameAttribute()
    {
        return "{$this->Lastname} {$this->othername}";
    }


    public function getNameAttribute()
    {
        return "{$this->Lastname} {$this->othername}";
    }

    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }

    // Add relationships as needed
}
