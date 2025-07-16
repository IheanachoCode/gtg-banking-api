<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'item_category';

    protected $fillable = ['category_name', 'image'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return config('app.image_url') . '/ITEM_IMAGE/' . $this->image;
    }
}
