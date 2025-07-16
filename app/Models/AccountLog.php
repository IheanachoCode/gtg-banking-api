<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;

class AccountLog extends Model
{
    use HasFactory;

  protected $table = 'account_log';
  protected $guarded = ['id'];

   
}