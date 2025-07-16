<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Loan;
use App\Http\Resources\LoanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class LoanController extends BaseController
{
    /**
     * Get user's loan history
     * 
     * Retrieve all loan records for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "loans": [
     *       {
     *         "id": 1,
     *         "userID": "USER123",
     *         "loan_amount": 50000.00,
     *         "interest_rate": 15.5,
     *         "duration": 12,
     *         "state": "Active",
     *         "created_at": "2024-01-01T10:30:00.000000Z",
     *         "updated_at": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The userID field is required",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getLoanHistory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching loan history', [
                'userID' => $request->userID
            ]);

            $loans = Loan::where('userID', $request->userID)->get();

            Log::info('Loan history fetched successfully', [
                'userID' => $request->userID,
                'count' => $loans->count()
            ]);

            return $this->successResponse([
                'loans' => LoanResource::collection($loans)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching loan history', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'An error occurred while fetching loan history', 
                500
            );
        }
    }

    /**
     * Get user's active loans
     * 
     * Retrieve only active loan records for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Request successfully completed",
     *   "data": {
     *     "loans": [
     *       {
     *         "id": 1,
     *         "userID": "USER123",
     *         "loan_amount": 50000.00,
     *         "interest_rate": 15.5,
     *         "duration": 12,
     *         "state": "Active",
     *         "created_at": "2024-01-01T10:30:00.000000Z",
     *         "updated_at": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The userID field is required",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getActiveLoans(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            Log::info('Fetching active loans', [
                'userID' => $request->userID
            ]);

            $loans = Loan::where('userID', $request->userID)
                        ->where('state', 'Active')
                        ->get();

            Log::info('Active loans fetched successfully', [
                'userID' => $request->userID,
                'count' => $loans->count()
            ]);

            return $this->successResponse([
                'loans' => LoanResource::collection($loans)
            ], 'Request successfully completed');

        } catch (\Exception $e) {
            Log::error('Error fetching active loans', [
                'userID' => $request->userID ?? 'not provided',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'An error occurred while fetching active loans', 
                500
            );
        }
    }
}