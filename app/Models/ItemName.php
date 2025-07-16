<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemName extends Model
{
    protected $table = 'item_name';
    
    protected $fillable = ['item', 'url'];
}