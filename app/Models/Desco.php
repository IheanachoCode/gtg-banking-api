<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Desco extends Model
{
    use HasFactory;

    protected $table = 'desco';
    
    protected $fillable = [
        'name',
        'code',
        'status'
    ];
}