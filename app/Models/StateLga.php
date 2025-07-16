<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateLga extends Model
{
    protected $table = 'state_lga';
    
    protected $fillable = ['state', 'lga'];
}