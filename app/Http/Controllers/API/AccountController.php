<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientRegistration;
use App\Models\AccountNumber;
use App\Models\ClientDepositWithdrawal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\AccountNameRequest;
use App\Services\AccountService;
use App\Http\Requests\FetchAccountTypeRequest;
use App\Http\Requests\FetchAccountStatusRequest;
use App\Http\Requests\FetchAccountNumberRequest;




class AccountController extends Controller
{
    use ApiResponse;

     protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getBalance($account_no)
    {
        $account = AccountNumber::where('account_no', $account_no)->first();

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $credit = ClientDepositWithdrawal::where('account_no', $account_no)
            ->where('transaction_type', 'Credit')
            ->sum('amount');

        $debit = ClientDepositWithdrawal::where('account_no', $account_no)
            ->where('transaction_type', 'Debit')
            ->sum('amount');

        $balance = $credit - $debit;

        return response()->json(['balance' => $balance]);
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getStatement($account_no)
    {
        $validator = Validator::make(['account_no' => $account_no], [
            'account_no' => 'required|exists:account_number,account_no'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transactions = ClientDepositWithdrawal::where('account_no', $account_no)
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getTransactions($account_no)
    {
        $validator = Validator::make(['account_no' => $account_no], [
            'account_no' => 'required|exists:account_number,account_no'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transactions = ClientDepositWithdrawal::where('account_no', $account_no)
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_account_no' => 'required|exists:account_number,account_no',
            'receiver_account_no' => 'required|exists:account_number,account_no',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'pin' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if sender has sufficient balance
        $senderBalance = $this->getBalance($request->sender_account_no);
        if ($senderBalance['balance'] < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }

        // Verify PIN
        $sender = ClientRegistration::whereHas('accounts', function($query) use ($request) {
            $query->where('account_no', $request->sender_account_no);
        })->first();

        if (!$sender || $sender->Pin !== $request->pin) {
            return response()->json(['message' => 'Invalid PIN'], 422);
        }

        DB::beginTransaction();
        try {
            // Generate transaction reference
            $txRef = 'Txid-' . rand(100000, 999999) . '-' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);

            // Record sender's debit
            ClientDepositWithdrawal::create([
                'account_no' => $request->sender_account_no,
                'amount' => $request->amount,
                'transaction_type' => 'Debit',
                'description' => 'Transfer to ' . $request->receiver_account_no . ' - ' . $request->description,
                'transaction_reference' => $txRef,
                'transaction_date' => now(),
            ]);

            // Record receiver's credit
            ClientDepositWithdrawal::create([
                'account_no' => $request->receiver_account_no,
                'amount' => $request->amount,
                'transaction_type' => 'Credit',
                'description' => 'Transfer from ' . $request->sender_account_no . ' - ' . $request->description,
                'transaction_reference' => $txRef,
                'transaction_date' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Transfer successful', 'reference' => $txRef]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Transfer failed'], 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function withdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|exists:account_number,account_no',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'pin' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if account has sufficient balance
        $balance = $this->getBalance($request->account_no);
        if ($balance['balance'] < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }

        // Verify PIN
        $account = ClientRegistration::whereHas('accounts', function($query) use ($request) {
            $query->where('account_no', $request->account_no);
        })->first();

        if (!$account || $account->Pin !== $request->pin) {
            return response()->json(['message' => 'Invalid PIN'], 422);
        }

        DB::beginTransaction();
        try {
            $txRef = 'Txid-' . rand(100000, 999999) . '-' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);

            ClientDepositWithdrawal::create([
                'account_no' => $request->account_no,
                'amount' => $request->amount,
                'transaction_type' => 'Debit',
                'description' => $request->description,
                'transaction_reference' => $txRef,
                'transaction_date' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Withdrawal successful', 'reference' => $txRef]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Withdrawal failed'], 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAccountName(Request $request)
    {
        try {
            Log::info('Account name lookup request received', [
                'userID' => $request->userID,
                'ip' => $request->ip()
            ]);

            $validator = Validator::make($request->all(), [
                'userID' => 'required|exists:client_registrations,user_id'
            ], [
                'userID.required' => 'User ID is required',
                'userID.exists' => 'Invalid User ID provided'
            ]);

            if ($validator->fails()) {
                Log::warning('Account name lookup validation failed', [
                    'userID' => $request->userID,
                    'errors' => $validator->errors()->toArray()
                ]);
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $user = ClientRegistration::where('user_id', $request->userID)->first();

            if (!$user) {
                Log::info('User not found', ['userID' => $request->userID]);
                return $this->errorResponse('User not found', 404);
            }

            $fullName = trim(sprintf(
                '%s %s %s',
                $user->lastname ?? '',
                $user->firstname ?? '',
                $user->othernames ?? ''
            ));

            Log::info('Account name retrieved successfully', [
                'userID' => $request->userID,
                'full_name' => $fullName
            ]);

            return $this->successResponse([
                'full_name' => $fullName
            ], 'Account name retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error in getAccountName', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('An error occurred while fetching account name', 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAllAccounts(Request $request)
    {
        try {
            Log::info('Fetching accounts request received', [
                'userID' => $request->userID,
                'ip' => $request->ip()
            ]);

            $validator = Validator::make($request->all(), [
                'userID' => 'required|exists:client_registrations,user_id'
            ], [
                'userID.required' => 'User ID is required',
                'userID.exists' => 'Invalid User ID provided'
            ]);

            if ($validator->fails()) {
                Log::warning('Account fetch validation failed', [
                    'userID' => $request->userID,
                    'errors' => $validator->errors()->toArray()
                ]);
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $accounts = AccountNumber::where('user_id', $request->userID)
                ->select(
                    'account_no',
                    'account_type',
                    'account_status',
                    DB::raw('DATE_FORMAT(date_created, "%Y-%m-%d %H:%i:%s") as opening_date')
                )
                ->get();

            Log::info('Accounts fetched successfully', [
                'userID' => $request->userID,
                'count' => $accounts->count()
            ]);

            if ($accounts->isEmpty()) {
                return $this->successResponse([
                    'accounts' => []
                ], 'No accounts found for this user');
            }

            return $this->successResponse([
                'accounts' => $accounts
            ], 'Accounts retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error in getAllAccounts', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('An error occurred while fetching accounts', 500);
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAccountBalance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required|string|exists:account_number,account_no'
            ], [
                'AccountNo.required' => 'Account number is required',
                'AccountNo.exists' => 'Invalid account number'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching account balance', [
                'account_no' => $request->AccountNo
            ]);

            $creditTotal = DB::table('client_deposit_withdrawal')
                ->where('account_no', $request->AccountNo)
                ->where('transaction_type', 'Credit')
                ->sum('amount') ?? 0;

            $debitTotal = DB::table('client_deposit_withdrawal')
                ->where('account_no', $request->AccountNo)
                ->where('transaction_type', 'Debit')
                ->sum('amount') ?? 0;

            $balance = $creditTotal - $debitTotal;

            // Generate a one-time key for this response
            $responseKey = bin2hex(random_bytes(16));

            // Encrypt sensitive data
            $encryptedData = Crypt::encrypt([
                'balance' => $balance,
                'credit_total' => $creditTotal,
                'debit_total' => $debitTotal,
                'timestamp' => now()->timestamp,
                'account_no' => $request->AccountNo
            ]);

            // Store the key briefly
            Cache::put(
                "balance_key_{$request->AccountNo}",
                $responseKey,
                now()->addMinutes(5)
            );

            return $this->successResponse([
                'data' => $encryptedData,
                'key_id' => "balance_key_{$request->AccountNo}"
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching account balance', [
                'account_no' => $request->AccountNo ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while fetching account balance',
                500
            );
        }
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getAccountNameByAccountNo(AccountNameRequest $request)
    {
        try {
            $fullName = $this->accountService->getAccountHolderName($request->account_no);

            if (!$fullName) {
                return $this->errorResponse('Account not found', 404, [
                    'account_no' => $request->account_no
                ]);
            }

            return $this->successResponse([
                'account_no' => $request->account_no,
                'account_name' => $fullName
            ], 'Account name fetched successfully');

        } catch (\Exception $e) {
            $errorRef = uniqid('err_');
            Log::error('Account name fetch error', [
                'error' => $e->getMessage(),
                'account_no' => $request->account_no,
                'error_reference' => $errorRef
            ]);

            return $this->errorResponse(
                'Failed to fetch account name. Reference: ' . $errorRef,
                500
            );
        }
    }

    //     public function getAccountNameByAccountNo(Request $request)
    // {
    //     try {
    //         $accountNo = $request->query('account_no');
    //         if (!$accountNo) {
    //             return $this->errorResponse('Account number is required', 422);
    //         }

    //         $fullName = $this->accountService->getAccountHolderName($accountNo);

    //         if (!$fullName) {
    //             return $this->errorResponse('Account not found', 404, [
    //                 'account_no' => $accountNo
    //             ]);
    //         }

    //         return $this->successResponse([
    //             'account_no' => $accountNo,
    //             'account_name' => $fullName
    //         ], 'Account name fetched successfully');

    //     } catch (\Exception $e) {
    //         $errorRef = uniqid('err_');
    //         Log::error('Account name fetch error', [
    //             'error' => $e->getMessage(),
    //             'account_no' => $accountNo ?? null,
    //             'error_reference' => $errorRef
    //         ]);

    //         return $this->errorResponse(
    //             'Failed to fetch account name. Reference: ' . $errorRef,
    //             500
    //         );
    //     }
    // }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getType(FetchAccountTypeRequest $request)
    {
        $result = $this->accountService->getAccountType($request->account_no);
        return response()->json($result);
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getStatus(FetchAccountStatusRequest $request)
    {
        $result = $this->accountService->getAccountStatus($request->account_no);
        return response()->json($result);
    }

    /**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getNumber(FetchAccountNumberRequest $request)
    {
        $result = $this->accountService->getAccountNumber(
            $request->user_id,
            $request->password
        );
        return response()->json($result);
    }
}
