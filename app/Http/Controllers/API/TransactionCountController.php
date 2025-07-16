<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionCountRequest;
use App\Services\TransactionCountService;
use Illuminate\Http\JsonResponse;
/**
 * @OA\Schema(
 *   schema="TransactionCountRequest",
 *   type="object",
 *   required={"account_no", "transaction_date"},
 *   @OA\Property(property="account_no", type="string", example="1000000001"),
 *   @OA\Property(property="transaction_date", type="string", format="date", example="2024-06-25")
 * )
 */

class TransactionCountController extends Controller
{
    protected $transactionCountService;

    public function __construct(TransactionCountService $transactionCountService)
    {
        $this->transactionCountService = $transactionCountService;
    }

    /**
     * Get transaction count for a specific date
     * 
     * Retrieve the number of transactions for a given account and date.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body transaction_date date required The transaction date (YYYY-MM-DD)
     * @response 200 {
     *   "status": true,
     *   "message": "Transaction count retrieved successfully",
     *   "data": {
     *     "account_no": "1000000001",
     *     "transaction_date": "2024-06-25",
     *     "count": 5
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function count(TransactionCountRequest $request): JsonResponse
    {
        $result = $this->transactionCountService->getTransactionCount(
            $request->account_no,
            $request->transaction_date
        );
        return response()->json($result);
    }
}
