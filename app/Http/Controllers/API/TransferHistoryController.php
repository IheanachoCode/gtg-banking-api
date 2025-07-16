<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferHistoryRequest;
use App\Services\TransferHistoryService;
use App\Http\Requests\TransferHistoryBetweenDatesRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TransferHistoryController extends Controller
{
    use ApiResponse;

    protected $transferHistoryService;

    public function __construct(TransferHistoryService $transferHistoryService)
    {
        $this->transferHistoryService = $transferHistoryService;
    }

    /**
     * Fetch transfer history for an account
     * 
     * Retrieve transfer history for a specific account with optional date filtering.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required Account number to fetch history for
     * @body transaction_date date optional Transaction date to filter history (YYYY-MM-DD)
     * @response 200 {
     *   "status": true,
     *   "message": "Transfer history retrieved successfully",
     *   "data": {
     *     "transfers": [
     *       {
     *         "id": 1,
     *         "account_no": "1000000001",
     *         "amount": 5000.00,
     *         "transaction_type": "Transfer",
     *         "description": "Transfer to 1000000002",
     *         "transaction_date": "2024-01-15",
     *         "reference": "TXN123456"
     *       }
     *     ]
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(TransferHistoryRequest $request): JsonResponse
    {
        $result = $this->transferHistoryService->getHistory(
            $request->account_no,
            $request->transaction_date
        );
        return $this->successResponse($result);
    }

    /**
     * Fetch transfer history between dates
     * 
     * Retrieve transfer history for a specific account within a date range.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @body start date required Start date (YYYY-MM-DD)
     * @body end date required End date (YYYY-MM-DD)
     * @response 200 {
     *   "status": true,
     *   "message": "Transfer history retrieved successfully",
     *   "data": {
     *     "transfers": [
     *       {
     *         "id": 1,
     *         "account_no": "1000000001",
     *         "amount": 5000.00,
     *         "transaction_type": "Transfer",
     *         "description": "Transfer to 1000000002",
     *         "transaction_date": "2024-01-15",
     *         "reference": "TXN123456"
     *       }
     *     ],
     *     "period": {
     *       "start": "2024-01-01",
     *       "end": "2024-01-31"
     *     }
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function betweenDates(TransferHistoryBetweenDatesRequest $request): JsonResponse
    {
        $result = $this->transferHistoryService->getHistoryBetweenDates(
            $request->account_no,
            $request->start,
            $request->end
        );
        return $this->successResponse($result);
    }
}
