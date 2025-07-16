<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;

class ChartOfAccount extends Model
{
    use HasFactory;

  protected $table = 'chart_of_account';
  protected $guarded = ['id'];

   
}