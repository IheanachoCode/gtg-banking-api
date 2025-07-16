<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRegistrationRequest;
use App\Services\CustomerService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CustomerNameRequest;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use ApiResponse;

    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Register a new customer
     * 
     * Register a new customer with complete personal and account information.
     * 
     * @header Authorization string required Bearer token
     * @body lastname string required Customer's last name
     * @body othername string required Customer's other names
     * @body gender string required Customer's gender (Male/Female)
     * @body Nationality string required Customer's nationality
     * @body birthday date required Customer's date of birth (YYYY-MM-DD)
     * @body occupation string required Customer's occupation
     * @body phone string required Customer's phone number
     * @body email string required Customer's email address
     * @body residential_address string required Customer's residential address
     * @body residential_state string required Customer's residential state
     * @body residential_Local_govt string required Customer's residential local government
     * @body state_of_origin string required Customer's state of origin
     * @body local_govt_of_origin string required Customer's local government of origin
     * @body town_of_origin string required Customer's town of origin
     * @body bvn_no string required Customer's Bank Verification Number
     * @body marital_status string required Customer's marital status
     * @body account_type string required Type of account (Savings/Current)
     * @body means_of_identification string required Means of identification
     * @body identification_no string required Identification number
     * @body staffID_get string required Staff ID
     * @body next_of_kin_name string required Next of kin's name
     * @body next_of_kin_othernames string required Next of kin's other names
     * @body next_of_kin_address string required Next of kin's address
     * @body relationship string required Relationship with next of kin
     * @body sms_notification boolean required SMS notification preference
     * @body email_notification boolean required Email notification preference
     * @body office_address string required Customer's office address
     * @response 200 {
     *   "status": true,
     *   "message": "Customer registered successfully",
     *   "data": {
     *     "respond": true,
     *     "customer_id": 123
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "bvn_no": ["The bvn no has already been taken."]
     *   }
     * }
     * @response 500 {
     *   "status": false,
     *   "message": "Failed to register customer. Reference: err_abc123",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function register(CustomerRegistrationRequest $request): JsonResponse
    {
        try {
            $result = $this->customerService->registerCustomer($request->validated());
            if ($result['status']) {
                return $this->successResponse($result['data'], $result['message'] ?? 'Customer registered successfully');
            } else {
                return $this->errorResponse($result['message'] ?? 'Failed to register customer.', 422, $result['data']);
            }
        } catch (\Exception $e) {
            Log::error('Customer registration error', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return $this->errorResponse(
                'Failed to register customer. Reference: ' . uniqid('err_'),
                500
            );
        }
    }

    /**
     * Get customer name by account number
     * 
     * Retrieve customer name information using their account number.
     * 
     * @header Authorization string required Bearer token
     * @query account_no string required The customer's account number
     * @response 200 {
     *   "status": true,
     *   "message": "Customer name retrieved successfully",
     *   "data": {
     *     "customer_name": "John Doe",
     *     "account_no": "1000000001"
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Customer not found",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getName(CustomerNameRequest $request): JsonResponse
    {
        $result = $this->customerService->getCustomerName($request->account_no);
        
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message']);
        } else {
            return $this->errorResponse($result['message'], 404);
        }
    }
}