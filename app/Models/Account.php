<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;
use App\Models\Client;

class Account extends Model
{
    use HasFactory;

    protected $table = 'account_number';
    
    protected $fillable = [
        'account_no',
        'account_type',
        'account_status',
        'user_id' 
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'user_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_no', 'account_no');
    }

        public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }
}