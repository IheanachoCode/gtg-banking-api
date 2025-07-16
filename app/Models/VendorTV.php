<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorTV extends Model
{
    use HasFactory;

    protected $table = 'vendortv';
    
    protected $fillable = ['name'];
}