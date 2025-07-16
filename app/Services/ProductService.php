<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductService
{
    public function hasAvailableProducts(): bool
    {
        return DB::table('stockin_table')
            ->where('quantity', '>', 0)
            ->exists();
    }

    public function getAvailableProducts(): array
    {
        return DB::table('stockin_table as a')
            ->join('item_name as b', 'a.item', '=', 'b.item')
            ->select([
                'a.item',
                'a.item_category',
                'b.url',
                'a.item_description',
                'a.selling_price'
            ])
            ->where('a.quantity', '>', 0)
            ->get()
            ->map(function ($product) {
                return [
                    'item' => $product->item,
                    'item_category' => $product->item_category,
                    'url' => $product->url,
                    'item_description' => $product->item_description,
                    'selling_price' => number_format($product->selling_price, 2)
                ];
            })
            ->toArray();
    }
}