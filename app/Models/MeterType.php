<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeterType extends Model
{
    use HasFactory;

    protected $table = 'metertype';
    
    protected $fillable = ['type'];
}