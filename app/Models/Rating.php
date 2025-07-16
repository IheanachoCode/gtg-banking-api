<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rating extends Model
{
    protected $table = 'rating';

    protected $fillable = [
        'name',
        'overall_satisfaction',
        'professionalism',
        'knowledge',
        'takes_ownership',
        'understands_myneeds',
        'comments',
        'rated_by',
        'rate_reference'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rating) {
            $rating->rate_reference = 'rate_' . rand(100000, 999999);
        });
    }
}
