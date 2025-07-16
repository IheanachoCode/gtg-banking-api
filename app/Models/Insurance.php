<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $table = 'insurance';

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'address',
        'business_sector',
        'tax_identification_no',
        'insurance_id',
        'option',
        'option_price',
        'insured_benefits',
        'status',
        'insurance_type',
    ];

    protected $casts = [
    'created_at' => 'datetime',
    'option_price' => 'decimal:2'
];

}
