<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'slider_image';

    protected $fillable = ['imageContent'];
}
