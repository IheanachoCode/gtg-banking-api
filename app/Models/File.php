<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'file_table';
    
    protected $fillable = [
        'file_source',
        'form',
        'url',
        'user_id',
        'staff_id',
        'date_created'
    ];

    public $timestamps = false;
}