<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'client_registrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_officer',
        'user_id',
        'lastname',
        'othernames',
        'email',
        'phone',
        'password',
        'account_type',
        'verification_status',
        'account_status',
        'last_login_at',
        'last_login_ip'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'birthday' => 'date',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->lastname} {$this->othernames}");
    }

    /**
     * Relationship with account officer.
     */
    public function accountOfficer()
    {
        return $this->belongsTo(Staff::class, 'account_officer', 'staffID');
    }

    /**
     * Relationship with account.
     */
    public function account()
    {
        return $this->hasOne(Account::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's tokens.
     */
    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }
}
