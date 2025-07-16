<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_category';

    protected $fillable = ['category', 'sub_categories', 'image'];

    protected $appends = ['image_url', 'category_url'];

    public function getImageUrlAttribute()
    {
        return config('app.image_url') . '/ITEM_IMAGE/' . $this->image;
    }

    public function getCategoryUrlAttribute()
    {
        return config('app.image_url') . '/index.php?action=' . $this->category;
    }
}
