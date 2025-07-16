<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillPaymentRequest;
use App\Services\BillPaymentService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class BillPaymentController extends Controller
{
    use ApiResponse;
    
    protected $billPaymentService;

    public function __construct(BillPaymentService $billPaymentService)
    {
        $this->billPaymentService = $billPaymentService;
    }

    /**
     * Process mobile bill payment
     * 
     * Process a mobile bill payment for airtime, data, or other mobile services.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body amount number required The payment amount
     * @body bill_type string required The type of bill (airtime, data, etc.)
     * @body phone string required The phone number for the bill payment
     * @response 200 {
     *   "status": true,
     *   "message": "Bill payment processed successfully",
     *   "data": {
     *     "response": {
     *       "success": true,
     *       "transaction_id": "TXN123456",
     *       "amount": 5000,
     *       "phone": "08012345678",
     *       "bill_type": "airtime",
     *       "status": "completed",
     *       "timestamp": "2024-01-01T10:30:00.000000Z"
     *     }
     *   }
     * }
     * @response 200 {
     *   "status": true,
     *   "message": "Bill payment failed",
     *   "data": {
     *     "response": false
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function processBillPayment(BillPaymentRequest $request): JsonResponse
    {
        try {
            $success = $this->billPaymentService->processBillPayment(
                $request->validated()
            );

            if ($success) {
                return $this->successResponse([
                    'response' => true
                ], 'Bill payment processed successfully');
            } else {
                // This branch is for when DB insert fails, not for validation errors
                return $this->successResponse([
                    'response' => false
                ], 'Bill payment failed');
            }
        } catch (\Exception $e) {
            Log::error('Bill payment error', [
                'error' => $e->getMessage(),
                'user' => $request->userID ?? null
            ]);
            // Always return a successResponse with status false and response false in data
            return $this->successResponse([
                'response' => false
            ], 'Bill payment failed: ' . $e->getMessage());
        }
    }
}