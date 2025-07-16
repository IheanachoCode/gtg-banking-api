<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryItemRequest;
use App\Services\ItemService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{

    use ApiResponse;
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Get items by subcategory
     * 
     * Retrieve all items for a specific subcategory.
     * 
     * @header Authorization string required Bearer token
     * @body item_name string required The subcategory name to get items for
     * @response 200 {
     *   "status": true,
     *   "message": "Items retrieved successfully",
     *   "data": {
     *     "items": [
     *       {
     *         "id": 1,
     *         "name": "MacBook Pro",
     *         "description": "High-performance laptop",
     *         "price": 1299.99,
     *         "category": "Laptops",
     *         "image": "https://example.com/macbook.jpg"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "No items found for this category",
     *   "data": {
     *     "items": []
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getCategoryItems(CategoryItemRequest $request): JsonResponse
    {
        $result = $this->itemService->getCategoryItems($request->item_name);

        if ($result['status']) {
            return $this->successResponse(
                ['items' => $result['data']],
                $result['message']
            );
        } else {
            return $this->errorResponse(
                $result['message'],
                404,
                ['items' => []]
            );
        }
    }

    /**
     * Get item details by item code
     * 
     * Retrieve detailed information about a specific item.
     * 
     * @header Authorization string required Bearer token
     * @query item_code string required The unique item code
     * @response 200 {
     *   "status": true,
     *   "message": "Item details fetched successfully",
     *   "data": {
     *     "item": {
     *       "id": 1,
     *       "item_code": "LAP001",
     *       "name": "MacBook Pro",
     *       "description": "High-performance laptop with M2 chip",
     *       "price": 1299.99,
     *       "category": "Laptops",
     *       "brand": "Apple",
     *       "specifications": {
     *         "processor": "M2",
     *         "ram": "16GB",
     *         "storage": "512GB SSD"
     *       },
     *       "image": "https://example.com/macbook.jpg"
     *     }
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Item not found",
     *   "data": {
     *     "item": null
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getItemDetails(Request $request): JsonResponse
    {
        $itemCode = $request->input('item_code');
        $result = $this->itemService->getItemDetails($itemCode);

        $item = $result['data'][0] ?? null;

        if ($item) {
            return $this->successResponse(
                ['item' => $item],
                $result['message']
            );
        } else {
            return $this->errorResponse(
                $result['message'],
                404,
                ['item' => null]
            );
        }
    }
}
