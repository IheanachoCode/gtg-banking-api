<?php

namespace App\Services;

use App\Models\MobileRequest;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function createOrder(array $data): array
    {
        try {
            $order = MobileRequest::create([
                'item_name' => $data['product_name'],
                'tem_code' => $data['item_code'],
                'description' => $data['description'],
                'account_no' => $data['account_no'],
                'quantity' => $data['Qty'],
                'price' => $data['price'],
                'total' => $data['total'],
                'status' => 'unapproved'
            ]);

            return [
                'status' => (bool) $order,
                'message' => $order ? 'Order created successfully.' : 'Failed to create order.',
                'data' => $order ? $order->toArray() : null
            ];

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'status' => false,
                'message' => 'Failed to create order.',
                'data' => null
            ];
        }
    }
}
