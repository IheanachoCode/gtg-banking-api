<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ViewInsuranceRequest;
use Illuminate\Http\JsonResponse;

class InsuranceController extends Controller
{
    use ApiResponse;

    /**
     * View user insurance details
     * 
     * Retrieve all insurance records for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Insurance records retrieved successfully",
     *   "data": {
     *     "fetchmember_error": false,
     *     "fetch_message": "Request successfully completed",
     *     "fetchmessage": [
     *       {
     *         "name": "John Doe",
     *         "phone_number": "+2348012345678",
     *         "email": "john@example.com",
     *         "address": "123 Main St",
     *         "business_sector": "Technology",
     *         "tax_identification_no": "TIN123456",
     *         "insurance_id": "INS001",
     *         "option": "Premium",
     *         "option_price": 50000.00,
     *         "insured_benefits": "Health coverage",
     *         "status": "Active",
     *         "insurance_type": "Health",
     *         "created_at": "2024-01-01T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "No insurance records found",
     *   "data": null
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "User not found or phone number missing",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function viewInsurance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'userID' => 'required|string|exists:client_registrations,user_id'
            ]);

            // Get user's phone number
            $user = DB::table('client_registrations')
                ->where('user_id', $request->userID)
                ->first();

            if (!$user || !$user->phone) {
                return $this->errorResponse('User not found or phone number missing', 404);
            }

            // Get insurance records
            $insuranceRecords = Insurance::where('phone_number', $user->phone)
                ->get()
                ->map(function ($insurance) {
                    return [
                        'name' => $insurance->name,
                        'phone_number' => $insurance->phone_number,
                        'email' => $insurance->email,
                        'address' => $insurance->address,
                        'business_sector' => $insurance->business_sector,
                        'tax_identification_no' => $insurance->tax_identification_no,
                        'insurance_id' => $insurance->insurance_id,
                        'option' => $insurance->option,
                        'option_price' => $insurance->option_price,
                        'insured_benefits' => $insurance->insured_benefits,
                        'status' => $insurance->status,
                        'insurance_type' => $insurance->insurance_type,
                        'created_at' => $insurance->created_at
                    ];
                });

            if ($insuranceRecords->isEmpty()) {
                return $this->errorResponse('No insurance records found', 404);
            }

            return $this->successResponse([
                'fetchmember_error' => false,
                'fetch_message' => 'Request successfully completed',
                'fetchmessage' => $insuranceRecords
            ], 'Insurance records retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Insurance view error', [
                'error' => $e->getMessage(),
                'userID' => $request->userID ?? null
            ]);

            return $this->errorResponse('Failed to retrieve insurance records', 500);
        }
    }

    /**
     * View active insurance details
     * 
     * Retrieve only active insurance records for a specific user.
     * 
     * @header Authorization string required Bearer token
     * @body userID string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Active insurance records retrieved successfully",
     *   "data": {
     *     "fetchmember_error": false,
     *     "fetch_message": "Request successfully completed",
     *     "fetchmessage": [
     *       {
     *         "name": "John Doe",
     *         "phone_number": "+2348012345678",
     *         "email": "john@example.com",
     *         "address": "123 Main St",
     *         "business_sector": "Technology",
     *         "tax_identification_no": "TIN123456",
     *         "insurance_id": "INS001",
     *         "option": "Premium",
     *         "option_price": 50000.00,
     *         "insured_benefits": "Health coverage",
     *         "status": "Active",
     *         "insurance_type": "Health",
     *         "created_at": "2024-01-01 10:30:00"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "No active insurance records found",
     *   "data": null
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "User not found or phone number missing",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function viewActiveInsurance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'userID' => 'required|string|exists:client_registrations,user_id'
            ]);

            // Get user's phone number
            $user = DB::table('client_registrations')
                ->where('user_id', $request->userID)
                ->first();

            if (!$user || !$user->phone) {
                return $this->errorResponse('User not found or phone number missing', 404);
            }

            // Get active insurance records
            $activeInsurance = Insurance::where('phone_number', $user->phone)
                ->where('status', 'Active')
                ->get()
                ->map(function ($insurance) {
                    return [
                        'name' => $insurance->name,
                        'phone_number' => $insurance->phone_number,
                        'email' => $insurance->email,
                        'address' => $insurance->address,
                        'business_sector' => $insurance->business_sector,
                        'tax_identification_no' => $insurance->tax_identification_no,
                        'insurance_id' => $insurance->insurance_id,
                        'option' => $insurance->option,
                        'option_price' => $insurance->option_price,
                        'insured_benefits' => $insurance->insured_benefits,
                        'status' => $insurance->status,
                        'insurance_type' => $insurance->insurance_type,
                        'created_at' => $insurance->created_at->format('Y-m-d H:i:s')
                    ];
                });

            if ($activeInsurance->isEmpty()) {
                return $this->errorResponse('No active insurance records found', 404);
            }

            return $this->successResponse([
                'fetchmember_error' => false,
                'fetch_message' => 'Request successfully completed',
                'fetchmessage' => $activeInsurance
            ], 'Active insurance records retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Active insurance view error', [
                'error' => $e->getMessage(),
                'userID' => $request->userID ?? null
            ]);

            return $this->errorResponse('Failed to retrieve active insurance records', 500);
        }
    }
}
