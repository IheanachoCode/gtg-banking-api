<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'stockin_table';

    protected $fillable = [
        'item',
        'item_category',
        'item_description',
        'selling_price',
        'quantity'
    ];

    public function itemDetails()
    {
        return $this->belongsTo(ItemName::class, 'item', 'item');
    }

     public function scopeFindByField($query, string $field, $value)
    {
        return $query->where($field, $value)->first();
    }
}
