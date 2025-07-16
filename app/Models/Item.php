<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item_name';

    protected $fillable = [
        'url',
        'item',
        'item_code',
        'item_model',
        'description',
        'selling_price',
        'sub_category'
    ];

    protected $appends = ['image_url', 'formatted_price'];

    public function getImageUrlAttribute()
    {
        return config('app.image_url') . '/ITEM_IMAGE/' . $this->url;
    }


    public function getFormattedPriceAttribute()
    {
        return number_format($this->selling_price, 2);
    }


}
