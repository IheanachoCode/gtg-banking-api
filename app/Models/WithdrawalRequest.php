<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $table = 'cash_withdrawal_mobileapp';
    
    protected $fillable = [
        'Account_no',
        'Account_name',
        'Account_type',
        'Account_officer',
        'Amount',
        'description',
        'commision_charges',
        'user_id',
        'Ref_no',
        'status',
        'Transaction_date',
        'staff_id'
    ];

    protected static function generateRefNo(): string
    {
        $numbers = substr(str_shuffle('0123456789'), 0, 6);
        $letters = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
        return 'Txid-' . $numbers . '-' . $letters;
    }
}