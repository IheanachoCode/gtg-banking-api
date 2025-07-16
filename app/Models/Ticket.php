<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket_table';

    protected $fillable = [
        'type',
        'account_no',
        'description',
        'amount',
        'status',
        'transaction_id',
        'transaction_date'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->transaction_id = self::generateTransactionId();
        });
    }

    private static function generateTransactionId(): string
    {
        $numbers = substr(str_shuffle(str_repeat("0123456789", 6)), 0, 6);
        $letters = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3);
        return 'Txid-' . $numbers . '-' . $letters;
    }
}
