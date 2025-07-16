<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get user's order records
     * 
     * Retrieve all orders for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "orders": [
     *       {
     *         "id": 1,
     *         "product_name": "MacBook Pro",
     *         "item_code": "LAP001",
     *         "description": "High-performance laptop",
     *         "account_no": "1000000001",
     *         "quantity": 1,
     *         "price": 1299.99,
     *         "total": 1299.99,
     *         "status": "Active",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The userID field is required",
     *   "data": null
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching orders",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getUserOrders(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching orders for user', [
                'userID' => $request->userID
            ]);

            $orders = Order::where('account_id', $request->userID)->get();

            Log::info('Orders fetched successfully', [
                'userID' => $request->userID,
                'count' => $orders->count()
            ]);

            return $this->successResponse([
                'orders' => OrderResource::collection($orders)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching user orders', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching orders',
                500
            );
        }
    }

    /**
     * Get user's active orders
     * 
     * Retrieve all active orders for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "orders": [
     *       {
     *         "id": 1,
     *         "product_name": "MacBook Pro",
     *         "item_code": "LAP001",
     *         "description": "High-performance laptop",
     *         "account_no": "1000000001",
     *         "quantity": 1,
     *         "price": 1299.99,
     *         "total": 1299.99,
     *         "status": "Active",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The userID field is required",
     *   "data": null
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "An error occurred while fetching active orders",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getActiveOrders(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching active orders for user', [
                'userID' => $request->userID
            ]);

            $orders = Order::where('account_id', $request->userID)
                          ->where('status', 'Active')
                          ->get();

            Log::info('Active orders fetched successfully', [
                'userID' => $request->userID,
                'count' => $orders->count()
            ]);

            return $this->successResponse([
                'orders' => OrderResource::collection($orders)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching active orders', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching active orders',
                500
            );
        }
    }

    /**
     * Create new order request
     * 
     * Create a new order request for a product.
     * 
     * @header Authorization string required Bearer token
     * @body product_name string required The name of the product
     * @body item_code string optional The item code
     * @body description string optional Product description
     * @body account_no string required The account number
     * @body Qty integer required The quantity to order
     * @body price number optional The unit price
     * @body total number optional The total amount
     * @response 200 {
     *   "status": "success",
     *   "message": "Order successful"
     * }
     * @response 500 {
     *   "status": "error",
     *   "message": "Order failed"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = 'unapproved';

        $inserted = \DB::table('mobile_request')->insert([
            'item_name'   => $data['product_name'],
            'tem_code'    => $data['item_code'] ?? null,
            'description' => $data['description'] ?? null,
            'account_no'  => $data['account_no'],
            'quantity'    => $data['Qty'],
            'price'       => $data['price'] ?? null,
            'total'       => $data['total'] ?? null,
            'status'      => $status,
        ]);

        if ($inserted) {
            return $this->successResponse(
                null,
                'Order successful'
            );
        } else {
            return $this->errorResponse(
                'Order failed',
                500
            );
        }
    }
}
