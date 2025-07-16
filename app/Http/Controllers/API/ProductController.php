<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    use ApiResponse;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products
     * 
     * Retrieve a list of all products in the system.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "products": [
     *       {
     *         "id": 1,
     *         "item": "MacBook Pro",
     *         "item_code": "LAP001",
     *         "selling_price": 1299.99,
     *         "description": "High-performance laptop",
     *         "category": "Electronics",
     *         "is_available": true,
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching products",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAllProducts(): JsonResponse
    {
        try {
            $products = Product::all();

            Log::info('Products fetched', [
                'count' => $products->count()
            ]);

            return $this->successResponse([
                'products' => ProductResource::collection($products)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching products',
                500
            );
        }
    }

    /**
     * Get product information by item name
     * 
     * Retrieve detailed information about a specific product by its item name.
     * 
     * @header Authorization string required Bearer token
     * @body item string required The product item name
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "selling_price": 1299.99
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Product not found",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The item field is required",
     *   "data": null
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching product information",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getProductInfo(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching product info', [
                'item' => $request->item
            ]);

            // Find product by item name
            $product = Product::findByField('item', $request->item);

            if (!$product) {
                Log::warning('Product not found', ['item' => $request->item]);
                return $this->errorResponse('Product not found', 404);
            }

            // Find product by item code to verify
            $productByCode = Product::findByField('item_code', $product->item_code);

            if (!$productByCode) {
                Log::error('Product code mismatch', [
                    'item' => $request->item,
                    'code' => $product->item_code
                ]);
                return $this->errorResponse('Product information inconsistent', 500);
            }

            Log::info('Product info retrieved', [
                'item' => $request->item,
                'selling_price' => $productByCode->selling_price
            ]);

            return $this->successResponse([
                'selling_price' => $productByCode->selling_price
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching product info', [
                'item' => $request->item ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching product information',
                500
            );
        }
    }

    /**
     * Get all available products
     * 
     * Retrieve a list of all products that are currently available for purchase.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Available products fetched successfully",
     *   "data": {
     *     "available": true,
     *     "products": [
     *       {
     *         "id": 1,
     *         "item": "MacBook Pro",
     *         "item_code": "LAP001",
     *         "selling_price": 1299.99,
     *         "stock_quantity": 10,
     *         "is_available": true
     *       }
     *     ],
     *     "total": 1
     *   }
     * }
     * @response 200 {
     *   "status": true,
     *   "message": "No products available",
     *   "data": {
     *     "available": false,
     *     "products": [],
     *     "total": 0
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "Failed to fetch products. Reference: err_abc123",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAvailableProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getAvailableProducts();

            if (empty($products)) {
                return $this->successResponse([
                    'available' => false,
                    'products' => [],
                    'total' => 0
                ], 'No products available');
            }

            return $this->successResponse([
                'available' => true,
                'products' => $products,
                'total' => count($products)
            ], 'Available products fetched successfully');

        } catch (\Exception $e) {
            $errorRef = uniqid('err_');
            Log::error('Product fetch error', [
                'error' => $e->getMessage(),
                'error_reference' => $errorRef
            ]);

            return $this->errorResponse(
                'Failed to fetch products. Reference: ' . $errorRef,
                500
            );
        }
    }
}
