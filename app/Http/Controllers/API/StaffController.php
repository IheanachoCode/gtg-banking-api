<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\ClientDepositWithdrawal;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StaffNameRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StaffTransactionRequest;
use App\Http\Requests\StaffDailyTransactionsRequest;
use App\Services\StaffTransactionService;
use App\Http\Requests\StaffDailyDepositRequest;
use App\Http\Requests\StaffDailyWithdrawalRequest;
use App\Http\Requests\StaffOverageRequest;
use App\Services\StaffOverageService;
use App\Http\Requests\StaffShortageRequest;
use App\Services\StaffShortageService;
use App\Http\Requests\StaffAggregateRequest;
use App\Services\StaffAggregateService;
use App\Http\Requests\UnverifiedTransactionRequest;
use App\Http\Requests\VerifiedTransactionRequest;
use App\Services\VerifiedTransactionService;
use App\Http\Requests\StaffPinValidationRequest;
use App\Services\StaffPinValidationService;
use App\Http\Requests\StaffLoginRequest;
use App\Services\StaffService;
use App\Services\StaffDepositService;
use App\Http\Requests\DailyDepositRequest;
use App\Services\UnverifiedTransactionService;

/**
 * @group Staff
 * Endpoints for staff authentication, transactions, and account management.
 */
class StaffController extends Controller
{


    use ApiResponse;

    protected $transactionService;

     protected $staffPinService;

     protected $staffService;

     protected $staffDepositService;

     protected $staffOverageService;

     protected $staffShortageService;

     protected $staffAggregateService;

     protected $unverifiedTransactionService;

     protected $verifiedTransactionService;

    public function __construct(
        StaffService $staffService,
        StaffTransactionService $transactionService,
        StaffPinValidationService $staffPinService,
        StaffDepositService $staffDepositService,
        StaffOverageService $StaffOverageService,
        StaffShortageService $StaffShortageService,
        StaffAggregateService $StaffAggregateService,
        UnverifiedTransactionService $unverifiedTransactionService,
        VerifiedTransactionService $VerifiedTransactionService
    ) {
        $this->staffService = $staffService;
        $this->transactionService = $transactionService;
        $this->staffPinService = $staffPinService;
        $this->staffDepositService = $staffDepositService;
        $this->staffOverageService = $StaffOverageService;
        $this->staffShortageService = $StaffShortageService;
        $this->staffAggregateService = $StaffAggregateService;
        $this->unverifiedTransactionService = $unverifiedTransactionService;
        $this->verifiedTransactionService = $VerifiedTransactionService;
    }


    //  public function __construct(StaffTransactionService $transactionService)
    // {
    //     $this->transactionService = $transactionService;
    // }


    // public function __construct(StaffPinValidationService $staffPinService)
    // {
    //     $this->staffPinService = $staffPinService;
    // }


    /**
     * Staff login
     *
     * @security ApiKeyAuth
     */
    public function login(StaffLoginRequest $request)
    {
        try {
            $startTime = microtime(true);

            $loginData = $this->staffService->login(
                $request->staffID,
                $request->password
            );

            if (!$loginData) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => $loginData,
                'response_time' => round($responseTime, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Staff login error', [
                'error' => $e->getMessage(),
                'staffID' => $request->staffID
            ]);

            return $this->errorResponse('An error occurred during login', 500);
        }
    }


    /**
     * Get staff daily transactions
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getDailyTransactions(StaffDailyTransactionsRequest $request)
    {
        try {
            // Check if staff exists
            $staff = Staff::where('staffID', $request->staffID)->first();

            if (!$staff) {
                return $this->errorResponse(
                    'Staff not found',
                    404,
                    ['staffID' => $request->staffID]
                );
            }

            $result = $this->staffService->getDailyTransactions(
                $request->staffID,
                $request->date
            );

            return $this->successResponse([
                'staff_details' => [
                    'staff_id' => $staff->staffID,
                    'name' => $staff->Lastname . ' ' . $staff->othername
                ],
                'transactions' => $result['transactions'],
                'summary' => $result['summary']
            ], $result['transactions']->isEmpty()
                ? 'No transactions found for this date'
                : 'Daily transactions retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Staff daily transactions fetch error', [
                'error' => $e->getMessage(),
                'staffID' => $request->staffID,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to fetch daily transactions',
                500,
                ['error_reference' => uniqid('err_')]
            );
        }
    }




/**
 * @OA\Get(
 *     path="/api/v1/staff/daily-deposits",
 *     summary="Get staff daily deposits total",
 *     tags={"Staff"},
 *     @OA\Parameter(
 *         name="staffID",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="fetchmember_error", type="boolean"),
 *             @OA\Property(property="Fetch_Daily_staff_deposit", type="boolean"),
 *             @OA\Property(property="fetchmessage", type="number", format="float")
 *         )
 *     )
 * )
 */
// public function getDailyDeposits(StaffDailyDepositRequest $request)
// {
//     try {
//         $total = $this->transactionService->getDailyDepositsTotal(
//             $request->staffID,
//             $request->transDate
//         );

//         return $this->successResponse([
//             'fetchmember_error' => false,
//             'Fetch_Daily_staff_deposit' => true,
//             'fetchmessage' => $total
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Staff daily deposits fetch error', [
//             'error' => $e->getMessage(),
//             'staffID' => $request->staffID,
//             'date' => $request->transDate
//         ]);

//         return $this->errorResponse('Failed to fetch deposits total', 500);
//     }

// }

 /**
  * Get staff daily deposit total
  *
  * @security ApiKeyAuth
  * @security SanctumAuth
  */
 public function getDailyDeposit(StaffDailyDepositRequest $request)
    {
        try {
            // Verify staff exists
            $staff = Staff::where('staffID', $request->staffID)->first();

            if (!$staff) {
                return $this->errorResponse('Staff not found', 404);
            }

            $result = $this->staffDepositService->getDailyDepositTotal(
                $request->staffID,
                $request->transDate
            );

            return $this->successResponse([
                'staff_details' => [
                    'staff_id' => $staff->staffID,
                    'name' => $staff->Lastname . ' ' . $staff->othername
                ],
                'deposit_summary' => $result
            ], 'Daily deposit total retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Daily deposit fetch error', [
                'error' => $e->getMessage(),
                'staffID' => $request->staffID,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to fetch daily deposit total', 500);
        }
    }




    /**
     * Get staff name by ID
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getStaffName(StaffNameRequest $request)
    {
        try {
            $staff = DB::table('staff')
                ->where('staffID', $request->staffID)
                ->select('Lastname', 'othername')
                ->first();

            if (!$staff) {
                return $this->errorResponse('Staff not found', 404);
            }

            $fullName = trim($staff->Lastname . ' ' . $staff->othername);

            return $this->successResponse([
                'fullname' => $fullName
            ], 'Staff name fetched successfully');

        } catch (\Exception $e) {
            Log::error('Staff name fetch error', [
                'error' => $e->getMessage(),
                'staffID' => $request->staffID
            ]);

            return $this->errorResponse('Failed to fetch staff name', 500);
        }
    }






/**
 * @OA\Get(
 *     path="/api/v1/staff/transactions",
 *     summary="Get staff recent transactions",
 *     tags={"Staff"},
 *     @OA\Parameter(
 *         name="staffID",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="fetchmember_error", type="boolean"),
 *             @OA\Property(property="fetchmember_Recent_Staff_Transact", type="boolean"),
 *             @OA\Property(property="fetchmessage", type="array",
 *                 @OA\Items(type="object")
 *             )
 *         )
 *     )
 * )
 */
// public function getRecentTransactions(StaffTransactionRequest $request)
// {
//     try {
//         $transactions = DB::table('client_deposit_withdrawal')
//             ->where('staff_id', $request->staffID)
//             ->select([
//                 'account_no',
//                 'amount',
//                 'transaction_type',
//                 'transaction_id',
//                 'created_at'
//             ])
//             ->orderBy('id', 'DESC')
//             ->get()
//             ->map(function ($transaction) {
//                 return [
//                     'account_no' => $transaction->account_no,
//                     'amount' => number_format($transaction->amount, 2),
//                     'transaction_type' => $transaction->transaction_type,
//                     'transaction_id' => $transaction->transaction_id,
//                     'created_at' => $transaction->created_at
//                 ];
//             });

//         if ($transactions->isEmpty()) {
//             return $this->successResponse([

//                 'transactions' => []
//             ], 'No recent transactions found');
//         }

//         return $this->successResponse([
//             'transactions' => $transactions
//         ],  'Recent transactions fetched successfully');

//     } catch (\Exception $e) {
//         Log::error('Staff transactions fetch error', [
//             'error' => $e->getMessage(),
//             'staffID' => $request->staffID
//         ]);

//         return $this->errorResponse('Failed to fetch transactions', 500);
//     }
// }


/**
 * Get staff recent transactions
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getRecentTransactions(StaffTransactionRequest $request)
{
    try {
        // First check if staff exists
        $staffExists = Staff::where('staffID', $request->staffID)->exists();

        if (!$staffExists) {
            return $this->errorResponse('Staff not found', 404);
        }

        $transactions = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $request->staffID)
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'transaction_id',
                'created_at'
            ])
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($transaction) {
                return [
                    'account_no' => $transaction->account_no,
                    'amount' => number_format($transaction->amount, 2),
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_id' => $transaction->transaction_id,
                    'created_at' => $transaction->created_at
                ];
            });

        if ($transactions->isEmpty()) {
            return $this->successResponse([
                'staff_id' => $request->staffID,
                'transactions' => [],
                'total_count' => 0
            ], 'No recent transactions found');
        }

        return $this->successResponse([
            'staff_id' => $request->staffID,
            'transactions' => $transactions,
            'total_count' => $transactions->count()
        ], 'Recent transactions fetched successfully');

    } catch (\Exception $e) {
        Log::error('Staff transactions fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'trace' => $e->getTraceAsString()
        ]);

        return $this->errorResponse('Failed to fetch transactions', 500);
    }
}


/**
 * Get staff daily withdrawals total
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getDailyWithdrawals(StaffDailyWithdrawalRequest $request)
{
    try {
        // Check if staff exists
        $staff = Staff::where('staffID', $request->staffID)->first();
        if (!$staff) {
            return $this->errorResponse('Staff not found', 404, [
                'staffID' => $request->staffID
            ]);
        }

        $total = $this->transactionService->getDailyWithdrawalsTotal(
            $request->staffID,
            $request->transDate
        );

        return $this->successResponse([
            'staff_details' => [
                'staff_id' => $staff->staffID,
                'name' => $staff->Lastname . ' ' . $staff->othername
            ],
            'withdrawals_summary' => [
                'total_withdrawals' => number_format($total, 2),
                'date' => $request->transDate
            ]
        ], 'Daily withdrawals total retrieved successfully');

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Staff daily withdrawals fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'date' => $request->transDate,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch withdrawals total. Reference: ' . $errorRef,
            500
        );
    }
}

/**
 * Get staff overage total
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getOverage(StaffOverageRequest $request)
{
    try {
        // Check if staff exists
        $staff = Staff::where('staffID', $request->staffID)->first();
        if (!$staff) {
            return $this->errorResponse('Staff not found', 404, [
                'staffID' => $request->staffID
            ]);
        }

        $total = $this->staffOverageService->getStaffOverageTotal($request->staffID);

        return $this->successResponse([
            'staff_details' => [
                'staff_id' => $staff->staffID,
                'name' => $staff->Lastname . ' ' . $staff->othername
            ],
            'overage_summary' => [
                'total_overage' => number_format($total, decimals: 2)
            ]
        ], 'Staff overage total retrieved successfully');

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Staff overage fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch overage total. Reference: ' . $errorRef,
            500
        );
    }
}


/**
 * Get staff shortage total
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getShortage(StaffShortageRequest $request)
{
    try {
        // Check if staff exists
        $staff = Staff::where('staffID', $request->staffID)->first();
        if (!$staff) {
            return $this->errorResponse('Staff not found', 404, [
                'staffID' => $request->staffID
            ]);
        }

        $total = $this->staffShortageService->getStaffShortageTotal($request->staffID);

        return $this->successResponse([
            'staff_details' => [
                'staff_id' => $staff->staffID,
                'name' => $staff->Lastname . ' ' . $staff->othername
            ],
            'shortage_summary' => [
                'total_shortage' => number_format($total, 2)
            ]
        ], 'Staff shortage total retrieved successfully');

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Staff shortage fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch shortage total. Reference: ' . $errorRef,
            500
        );
    }
}




/**
 * Get staff aggregate balance
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getAggregate(StaffAggregateRequest $request)
{
    try {
        // Check if staff exists
        $staff = Staff::where('staffID', $request->staffID)->first();
        if (!$staff) {
            return $this->errorResponse('Staff not found', 404, [
                'staffID' => $request->staffID
            ]);
        }

        $aggregate = $this->staffAggregateService->getStaffAggregate($request->staffID);

        return $this->successResponse([
            'staff_details' => [
                'staff_id' => $staff->staffID,
                'name' => $staff->Lastname . ' ' . $staff->othername
            ],
            'aggregate_summary' => $aggregate
        ], 'Staff aggregate data retrieved successfully');

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Staff aggregate fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch staff aggregate. Reference: ' . $errorRef,
            500
        );
    }
}


/**
 * Get staff unverified transactions
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getUnverifiedTransactions(UnverifiedTransactionRequest $request)
{
    try {
        $transactions = $this->unverifiedTransactionService->getUnverifiedTransactions($request->staffID);

        return $this->successResponse([
            'staff_id' => $request->staffID,
            'total_unverified' => count($transactions),
            'unverified_transactions' => $transactions
        ], count($transactions) > 0
            ? 'Unverified transactions fetched successfully'
            : 'No unverified transactions found'
        );

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Unverified transactions fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch unverified transactions. Reference: ' . $errorRef,
            500
        );
    }
}




/**
 * Get staff verified transactions
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function getVerifiedTransactions(VerifiedTransactionRequest $request)
{
    try {
        $transactions = $this->verifiedTransactionService->getVerifiedTransactions(
            $request->staffID,
            $request->Select_Date
        );

        return $this->successResponse([
            'staff_id' => $request->staffID,
            'date' => $request->Select_Date,
            'total_verified' => count($transactions),
            'verified_transactions' => $transactions
        ], count($transactions) > 0
            ? 'Verified transactions fetched successfully'
            : 'No verified transactions found for this date'
        );

    } catch (\Exception $e) {
        $errorRef = uniqid('err_');
        Log::error('Verified transactions fetch error', [
            'error' => $e->getMessage(),
            'staffID' => $request->staffID,
            'date' => $request->Select_Date,
            'error_reference' => $errorRef
        ]);

        return $this->errorResponse(
            'Failed to fetch verified transactions. Reference: ' . $errorRef,
            500
        );
    }
}



/**
 * Validate staff PIN
 *
 * @security ApiKeyAuth
 * @security SanctumAuth
 */
public function validatePin(StaffPinValidationRequest $request)
{
    try {
        $isValid = $this->staffPinService->validatePin(
            $request->staff_id,
            $request->pin_no
        );

        return $this->successResponse([
            'response' => $isValid
        ]);

    } catch (\Exception $e) {
        Log::error('Staff PIN validation error', [
            'error' => $e->getMessage(),
            'staff' => $request->staff_id
        ]);

        return $this->errorResponse('Failed to validate PIN', 500);
    }
}






    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $staff = Staff::where('email', $request->email)->first();

    //     if (!$staff || !Hash::check($request->password, $staff->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     $token = $staff->createToken('staff-token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'staff' => $staff
    //     ]);
    // }

    /**
     * Get transactions between dates
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transactions = ClientDepositWithdrawal::whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * Get daily summary for staff
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getDailySummary()
    {
        $today = now()->format('Y-m-d');

        $summary = ClientDepositWithdrawal::whereDate('transaction_date', $today)
            ->selectRaw('transaction_type, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('transaction_type')
            ->get();

        return response()->json([
            'date' => $today,
            'summary' => $summary
        ]);
    }

    /**
     * Create a staff request
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|exists:account_number,account_no',
            'request_type' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requestModel = $request->user()->requests()->create([
            'account_no' => $request->account_no,
            'request_type' => $request->request_type,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Request created successfully',
            'request' => $requestModel
        ]);
    }

    /**
     * Rate an account officer
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function rateAccountOfficer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'overall_satisfaction' => 'required|integer',
            'professionalism' => 'required|integer',
            'knowledge' => 'required|integer',
            'takes_ownership' => 'required|integer',
            'understands_myneeds' => 'required|integer',
            'comments' => 'nullable|string',
            'rated_by' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rate = Feedback::create($request->all());

        return response()->json([
            'message' => 'Rating submitted successfully',
            'rate' => $rate
        ]);
    }

    /**
     * Submit staff feedback
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function submitFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'feedback_type' => 'required|string',
            'feature_impactd' => 'required|string',
            'feedback_comment' => 'required|string',
            'rate' => 'required|integer',
            'feedback_by' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create($request->all());

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ]);
    }

    /**
     * Upload feedback images
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function uploadFeedbackImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_no' => 'required|string|exists:feedback,reference_no',
            'first_image' => 'required|image',
            'second_image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::where('reference_no', $request->reference_no)->first();
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found'], 404);
        }

        if ($request->hasFile('first_image')) {
            $firstImagePath = $request->file('first_image')->store('feedback_images', 'public');
            $feedback->first_image_url = $firstImagePath;
        }
        if ($request->hasFile('second_image')) {
            $secondImagePath = $request->file('second_image')->store('feedback_images', 'public');
            $feedback->second_image_url = $secondImagePath;
        }
        $feedback->save();

        return response()->json([
            'message' => 'Images uploaded successfully',
            'feedback' => $feedback
        ]);
    }













}
