<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ClientDepositWithdrawal;
use App\Models\AccountNumber;
use App\Models\Transfer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransferResource;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserRecentTransactionsRequest;
use App\Services\UserTransactionService;
use App\Http\Requests\FetchTransactionDatesRequest;
use App\Http\Requests\FetchLimitedDatesRequest;
use App\Services\TransactionService;
use App\Http\Requests\TransactionDateRangeRequest;
use App\Http\Requests\DailyTransactionsRequest;
use App\Services\DailyTransactionService;
use Illuminate\Http\JsonResponse;



class TransactionController extends Controller
{
    use ApiResponse;

    protected $transactionService;
    protected $dailyTransactionService;

    public function __construct(UserTransactionService $transactionService, DailyTransactionService $dailyTransactionService)
    {
        $this->transactionService = $transactionService;
        $this->dailyTransactionService = $dailyTransactionService;
    }

    /**
     * Get transaction history for an account
     * 
     * Retrieve all transactions for a specific account number.
     * 
     * @header Authorization string required Bearer token
     * @param string $account_no The account number
     * @response 200 {
     *   "status": true,
     *   "message": "Transaction history retrieved successfully",
     *   "data": {
     *     "transactions": [
     *       {
     *         "id": 1,
     *         "account_no": "1000000001",
     *         "amount": 5000.00,
     *         "transaction_type": "Credit",
     *         "description": "Deposit",
     *         "transaction_reference": "TXN123456",
     *         "transaction_date": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The selected account no is invalid",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getTransactionHistory($account_no): JsonResponse
    {
        $validator = Validator::make(['account_no' => $account_no], [
            'account_no' => 'required|exists:account_number,account_no'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        
        $transactions = ClientDepositWithdrawal::where('account_no', $account_no)
            ->orderBy('transaction_date', 'desc')
            ->get();
            
        return $this->successResponse(['transactions' => $transactions], 'Transaction history retrieved successfully');
    }

    /**
     * Get transactions by date range
     * 
     * Retrieve transactions for a specific account within a date range.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body start_date date required Start date (YYYY-MM-DD)
     * @body end_date date required End date (YYYY-MM-DD)
     * @response 200 {
     *   "status": true,
     *   "message": "Transactions retrieved successfully",
     *   "data": {
     *     "transactions": [
     *       {
     *         "id": 1,
     *         "account_no": "1000000001",
     *         "amount": 5000.00,
     *         "transaction_type": "Credit",
     *         "description": "Deposit",
     *         "transaction_reference": "TXN123456",
     *         "transaction_date": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getTransactionsByDate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|exists:account_number,account_no',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        
        $transactions = ClientDepositWithdrawal::where('account_no', $request->account_no)
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date', 'desc')
            ->get();
            
        return $this->successResponse(['transactions' => $transactions], 'Transactions retrieved successfully');
    }

    /**
     * Pay a bill
     * 
     * Process a bill payment from the user's account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body amount number required The payment amount
     * @body bill_type string required The type of bill
     * @body bill_reference string required The bill reference number
     * @body description string required Payment description
     * @body pin string required Account PIN
     * @response 200 {
     *   "status": true,
     *   "message": "Bill payment successful",
     *   "data": {
     *     "message": "Bill payment successful",
     *     "reference": "Bill-123456-abc",
     *     "amount": 5000.00,
     *     "bill_type": "electricity"
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
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function payBill(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|exists:account_number,account_no',
            'amount' => 'required|numeric|min:0',
            'bill_type' => 'required|string',
            'bill_reference' => 'required|string',
            'description' => 'required|string',
            'pin' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        
        $account = AccountNumber::where('account_no', $request->account_no)->first();
        $credit = ClientDepositWithdrawal::where('account_no', $request->account_no)
            ->where('transaction_type', 'Credit')
            ->sum('amount');
        $debit = ClientDepositWithdrawal::where('account_no', $request->account_no)
            ->where('transaction_type', 'Debit')
            ->sum('amount');
        $balance = $credit - $debit;
        
        if ($balance < $request->amount) {
            return $this->errorResponse('Insufficient balance', 422);
        }
        
        $client = $account->client;
        if (!$client || $client->Pin !== $request->pin) {
            return $this->errorResponse('Invalid PIN', 422);
        }
        
        DB::beginTransaction();
        try {
            $txRef = 'Bill-' . rand(100000, 999999) . '-' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
            ClientDepositWithdrawal::create([
                'account_no' => $request->account_no,
                'amount' => $request->amount,
                'transaction_type' => 'Debit',
                'description' => $request->bill_type . ' Payment - ' . $request->description,
                'transaction_reference' => $txRef,
                'bill_reference' => $request->bill_reference,
                'transaction_date' => now(),
            ]);
            DB::commit();
            
            return $this->successResponse([
                'message' => 'Bill payment successful',
                'reference' => $txRef,
                'amount' => $request->amount,
                'bill_type' => $request->bill_type
            ], 'Bill payment successful');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Bill payment failed', 500);
        }
    }

    public function getUserTransfers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching transfer history', [
                'userID' => $request->userID
            ]);

            $transfers = Transfer::where('user_id', $request->userID)
                               ->where('payment_mode', 'Transfer')
                               ->orderBy('transaction_date', 'desc')
                               ->get();

            Log::info('Transfer history fetched successfully', [
                'userID' => $request->userID,
                'count' => $transfers->count()
            ]);

            return $this->successResponse([
                'transfers' => TransferResource::collection($transfers)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching transfer history', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching transfer history',
                500
            );
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    
    public function getRecentTransactions(UserRecentTransactionsRequest $request)
    {
        try {
            $transactions = $this->transactionService->getRecentTransactions(
                $request->account_no
            );
            return $this->successResponse([
                'status' => 'success',
                'message' => empty($transactions) ? 'No transactions found' : 'Recent transactions fetched successfully',
                'data' => [
                    'transactions' => $transactions
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Recent transactions fetch error', [
                'error' => $e->getMessage(),
                'account' => $request->account_no
            ]);
            return $this->errorResponse('Failed to fetch recent transactions', 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    
    public function getTransactionsByDateRange(TransactionDateRangeRequest $request)
    {
        try {
            $transactions = $this->transactionService->getTransactionsByDateRange(
                $request->account_no,
                $request->from,
                $request->to
            );
            return $this->successResponse([
                'fetchmember_error' => false,
                'Fetch_User_Recent_Transact' => true,
                'fetchmessage' => $transactions
            ]);
        } catch (\Exception $e) {
            Log::error('Transaction date range fetch error', [
                'error' => $e->getMessage(),
                'account' => $request->account_no,
                'from' => $request->from,
                'to' => $request->to
            ]);
            return $this->errorResponse('Failed to fetch transactions', 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */

    public function getDailyTransactions(DailyTransactionsRequest $request)
    {
        try {
            $transactions = $this->dailyTransactionService->getTodaysTransactions($request->date);
            return $this->successResponse([
                'date' => $request->date,
                'total_transactions' => count($transactions),
                'transactions' => $transactions
            ], count($transactions) > 0
                ? 'Daily transactions fetched successfully'
                : 'No transactions found for this date'
            );
        } catch (\Exception $e) {
            $errorRef = uniqid('err_');
            Log::error('Daily transactions fetch error', [
                'error' => $e->getMessage(),
                'date' => $request->date,
                'error_reference' => $errorRef
            ]);
            return $this->errorResponse(
                'Failed to fetch daily transactions. Reference: ' . $errorRef,
                500
            );
        }
    }

/**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getDates(FetchTransactionDatesRequest $request)
    {
        $result = $this->transactionService->getTransactionDates($request->account_no);
        return $this->successResponse(['dates' => $result], 'Transaction dates fetched successfully.');
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getLimitedDates(FetchLimitedDatesRequest $request)
    {
        $dates = $this->transactionService->getLimitedTransactionDates($request->account_no);
        return $this->successResponse(['dates' => $dates], 'Limited transaction dates fetched successfully.');
    }
}
