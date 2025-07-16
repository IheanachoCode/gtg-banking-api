<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    
    protected $fillable = [
        'name',
        'code',
        'status',
        // Add other vendor fields
    ];
}