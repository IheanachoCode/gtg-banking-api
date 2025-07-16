<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentUpdateService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;
    protected $paymentUpdateService;

    public function __construct(PaymentService $paymentService, PaymentUpdateService $paymentUpdateService)
    {
        $this->paymentService = $paymentService;
        $this->paymentUpdateService = $paymentUpdateService;
    }

    /**
     * Get all payment modes
     * 
     * Retrieve a list of all available payment modes in the system.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Payment modes fetched successfully",
     *   "data": {
     *     "payment_modes": [
     *       {
     *         "id": 1,
     *         "pay_mode": "Cash",
     *         "description": "Cash payment",
     *         "is_active": true
     *       },
     *       {
     *         "id": 2,
     *         "pay_mode": "Bank Transfer",
     *         "description": "Bank transfer payment",
     *         "is_active": true
     *       },
     *       {
     *         "id": 3,
     *         "pay_mode": "Card",
     *         "description": "Card payment",
     *         "is_active": true
     *       }
     *     ],
     *     "total_count": 3
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "Failed to fetch payment modes. Reference: err_abc123",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAllPaymentModes(): JsonResponse
    {
        try {
            $modes = $this->paymentService->getAllPaymentModes();

            return $this->successResponse([
                'payment_modes' => $modes,
                'total_count' => count($modes)
            ], 'Payment modes fetched successfully');

        } catch (\Exception $e) {
            $errorRef = uniqid('err_');
            Log::error('Payment modes fetch error', [
                'error' => $e->getMessage(),
                'error_reference' => $errorRef
            ]);

            return $this->errorResponse(
                'Failed to fetch payment modes. Reference: ' . $errorRef,
                500
            );
        }
    }

    /**
     * Verify mobile payment request
     * 
     * Verify and update the status of a mobile payment request.
     * 
     * @header Authorization string required Bearer token
     * @body ref_no string required The payment reference number
     * @response 200 {
     *   "status": true,
     *   "message": "Payment verification completed",
     *   "data": {
     *     "response": {
     *       "verified": true,
     *       "status": "completed",
     *       "amount": 1500.00,
     *       "transaction_date": "2024-01-01T10:30:00.000000Z"
     *     }
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Payment not found",
     *   "data": {
     *     "response": {
     *       "verified": false,
     *       "status": "not_found"
     *     }
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verifyPayment(UpdatePaymentRequest $request): JsonResponse
    {
        $success = $this->paymentUpdateService->updatePaymentStatus($request->ref_no);

        return $this->successResponse([
            'response' => $success
        ], 'Payment verification completed');
    }
}
