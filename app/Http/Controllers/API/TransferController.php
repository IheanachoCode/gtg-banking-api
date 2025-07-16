<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;
use App\Services\EmailService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    use ApiResponse;

    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Process a transfer between accounts
     * 
     * Transfer funds between accounts with validation and notifications.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body sender_account_no string required The sender's account number
     * @body amount_transfer number required The amount to transfer
     * @body sender_description string required Description for the transfer
     * @body Receiver_account_number string required The receiver's account number
     * @body Pin string required Account PIN for verification
     * @response 200 {
     *   "status": true,
     *   "message": "Transfer completed successfully",
     *   "data": {
     *     "transaction_id": "TXN-123-abc",
     *     "amount": 1000.00,
     *     "sender_account": "1234567890",
     *     "receiver_account": "0987654321",
     *     "transfer_date": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Insufficient balance",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Invalid PIN",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Receiver account not found",
     *   "data": null
     * }
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $result = $this->transferService->processTransfer($request->validated());

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 422);
        }

        return $this->successResponse($result['data'] ?? null, $result['message']);
    }
}
