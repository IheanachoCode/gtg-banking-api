<?php
namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class ItemService
{
    public function getCategoryItems(string $subCategory): array
    {
        try {
            $items = Item::where('sub_category', $subCategory)
                ->select('url', 'item', 'item_code', 'selling_price')
                ->get()
                ->map(function ($item) {
                    return [
                        'url' => $item->image_url,
                        'item' => $item->item,
                        'item_code' => $item->item_code,
                        'price' => $item->selling_price
                    ];
                })
                ->toArray();

            return [
                'status' => !empty($items),
                'message' => !empty($items) ? 'Items fetched successfully.' : 'No items found.',
                'data' => $items
            ];

        } catch (\Exception $e) {
            Log::error('Category items fetch failed', [
                'error' => $e->getMessage(),
                'sub_category' => $subCategory
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch items.',
                'data' => []
            ];
        }
    }

    public function getItemDetails(string $itemCode): array
    {
        try {
            $items = Item::where('item_code', $itemCode)
                ->select('url', 'item', 'item_code', 'item_model', 'description', 'selling_price')
                ->get()
                ->map(function ($item) {
                    return [
                        'url' => $item->image_url,
                        'item' => $item->item,
                        'item_code' => $item->item_code,
                        'item_model' => $item->item_model,
                        'description' => $item->description,
                        'selling_price' => $item->formatted_price
                    ];
                })
                ->toArray();

            return [
                'status' => !empty($items),
                'message' => !empty($items) ? 'Item details fetched successfully.' : 'No item found.',
                'data' => $items
            ];

        } catch (\Exception $e) {
            Log::error('Item details fetch failed', [
                'error' => $e->getMessage(),
                'item_code' => $itemCode
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch item details.',
                'data' => []
            ];
        }
    }
}