<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\WithdrawalRequestMobileRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;


class WithdrawalController extends Controller
{
    use ApiResponse;

    protected $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * Create withdrawal request
     * 
     * Create a new withdrawal request for the specified account.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @body AccountNo string required The account number
     * @body amount number required The withdrawal amount
     * @body description string required Description of the withdrawal
     * @response 200 {
     *   "status": true,
     *   "message": "Withdrawal request created successfully",
     *   "data": {
     *     "ref_no": "Txid-123456-abc",
     *     "amount": 5000.00,
     *     "charges": 100.00,
     *     "status": "Pending"
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Account not found",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function createWithdrawalRequest(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string|exists:client_registrations,user_id',
                'AccountNo' => 'required|string|exists:account_number,account_no',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            // Get account details
            $accountType = DB::table('account_number')
                ->where('account_no', $request->AccountNo)
                ->first();

            if (!$accountType) {
                return $this->errorResponse('Account not found', 404);
            }

            // Get user details
            $accountName = DB::table('client_registrations')
                ->where('user_id', $request->userID)
                ->first();

            if (!$accountName) {
                return $this->errorResponse('User not found', 404);
            }

            // Get transaction charges
            $charges = DB::table('account_constraints')
                ->where('account_type', $accountType->account_type)
                ->first();

            if (!$charges) {
                return $this->errorResponse('Account type constraints not found', 404);
            }

            // Generate reference number
            $refNo = 'Txid-' . substr(str_shuffle(str_repeat("0123456789", 6)), 0, 6) . '-'
                    . substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3);

            // Create withdrawal request
            $withdrawalRequest = DB::table('cash_withdrawal')->insert([
                'Account_no' => $request->AccountNo,
                'Account_name' => $accountName->lastname . ' ' . $accountName->othernames,
                'Account_type' => $accountType->account_type,
                'Account_officer' => 'No Staff',
                'Amount' => $request->amount,
                'description' => $request->description,
                'commision_charges' => $charges->transactions_charges,
                'user_id' => $request->userID,
                'Ref_no' => $refNo,
                'status' => 'Pending',
                'Transaction_date' => now()->format('Y-m-d'),
                'staff_id' => 'No Staff',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$withdrawalRequest) {
                return $this->errorResponse('Failed to create withdrawal request', 500);
            }

            // Log the withdrawal request
            Log::info('Withdrawal request created', [
                'user_id' => $request->userID,
                'account_no' => $request->AccountNo,
                'amount' => $request->amount,
                'ref_no' => $refNo
            ]);

            return $this->successResponse([
                'ref_no' => $refNo,
                'amount' => $request->amount,
                'charges' => $charges->transactions_charges,
                'status' => 'Pending'
            ], 'Withdrawal request created successfully');

        } catch (\Exception $e) {
            Log::error('Withdrawal request error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('An error occurred while processing withdrawal request', 500);
        }
    }

    /**
     * Process mobile withdrawal request
     * 
     * Process a withdrawal request from mobile application.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body amount number required The withdrawal amount
     * @body pin string required Account PIN for verification
     * @response 200 {
     *   "status": true,
     *   "message": "Withdrawal processed successfully",
     *   "data": {
     *     "transaction_id": "TXN123456",
     *     "amount": 5000.00,
     *     "balance": 45000.00,
     *     "status": "completed"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Insufficient balance",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function requestWithdrawal(WithdrawalRequestMobileRequest $request): JsonResponse
    {
        try {
            $result = $this->withdrawalService->processWithdrawalRequest(
                $request->validated()
            );

            return $this->successResponse($result);

        } catch (\Exception $e) {
            Log::error('Withdrawal request error', [
                'error' => $e->getMessage(),
                'user' => $request->userID
            ]);

            return $this->successResponse([
                'response_pin' => true,
                'response' => false
            ]);
        }
    }
}
