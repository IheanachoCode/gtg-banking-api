<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'name',
        'overall_satisfaction',
        'professionalism',
        'knowledge',
        'takes_ownership',
        'understands_myneeds',
        'comments',
        'rated_by',
        'feedback_type',
        'feature_impacted',
        'feedback_comment',
        'rate',
        'feedback_by',
        'reference_no',
        'first_image_url',
        'second_image_url',
    ];


     public function files()
    {
        return $this->hasMany(File::class, 'user_id', 'reference_no');
    }


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($feedback) {
            $feedback->reference_no = 'fb_' . rand(100000, 999999);
        });
    }

    // Add relationships as needed
} 