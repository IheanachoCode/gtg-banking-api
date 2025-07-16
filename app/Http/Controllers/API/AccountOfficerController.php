<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountOfficerNameRequest;
use App\Http\Requests\AccountOfficerPhoneRequest;
use App\Http\Requests\AccountOfficerEmailRequest;
use App\Services\AccountOfficerService;
use App\Http\Requests\FetchAccountOfficersRequest;
use App\Http\Requests\FetchAllAccountOfficersRequest;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class AccountOfficerController extends Controller
{

    use ApiResponse;
    
    protected $accountOfficerService;

    public function __construct(AccountOfficerService $accountOfficerService)
    {
        $this->accountOfficerService = $accountOfficerService;
    }

    /**
     * Get account officer name
     * 
     * Retrieve the name of the account officer assigned to a specific account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @response 200 {
     *   "officer_name": "John Doe",
     *   "account_no": "1000000001"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getName(AccountOfficerNameRequest $request): JsonResponse
    {
        $result = $this->accountOfficerService->getOfficerName($request->input('account_no'));
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Account officer name fetched successfully.');
        } else {
            return $this->errorResponse($result['message'] ?? 'Account officer not found.', 404, $result['data']);
        }
    }

    /**
     * Get account officer phone number
     * 
     * Retrieve the phone number of the account officer assigned to a specific account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @response 200 {
     *   "officer_phone": "+2348012345678",
     *   "account_no": "1000000001"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getPhone(AccountOfficerPhoneRequest $request): JsonResponse
    {
        $result = $this->accountOfficerService->getOfficerPhone($request->input('account_no'));
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Account officer phone fetched successfully.');
        } else {
            return $this->errorResponse($result['message'] ?? 'Account officer phone not found.', 404, $result['data']);
        }
    }

    /**
     * Get account officer email
     * 
     * Retrieve the email address of the account officer assigned to a specific account.
     * 
     * @header Authorization string required Bearer token
     * @body account_no string required The account number
     * @response 200 {
     *   "officer_email": "officer@example.com",
     *   "account_no": "1000000001"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function getEmail(AccountOfficerEmailRequest $request): JsonResponse
    {
        $result = $this->accountOfficerService->getOfficerEmail($request->input('account_no'));
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Account officer email fetched successfully.');
        } else {
            return $this->errorResponse($result['message'] ?? 'Account officer email not found.', 404, $result['data']);
        }
    }

    /**
     * Get all account officers
     * 
     * Retrieve a list of all account officers in the system.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "officers": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "phone": "+2348012345678",
     *       "email": "john@example.com"
     *     }
     *   ]
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(FetchAccountOfficersRequest $request): JsonResponse
    {
        $result = $this->accountOfficerService->getAllOfficers();
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Account officers fetched successfully.');
        } else {
            return $this->errorResponse($result['message'] ?? 'No account officers found.', 404, $result['data']);
        }
    }

    /**
     * Get all account officers with details
     * 
     * Retrieve a comprehensive list of all account officers with full details.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "officers": [
     *     {
     *       "id": 1,
     *       "name": "John Doe",
     *       "phone": "+2348012345678",
     *       "email": "john@example.com",
     *       "department": "Customer Service",
     *       "assigned_accounts": 25,
     *       "rating": 4.5
     *     }
     *   ]
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function all(FetchAllAccountOfficersRequest $request): JsonResponse
    {
        $result = $this->accountOfficerService->getAllOfficersWithDetails();
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message'] ?? 'Account officers with details fetched successfully.');
        } else {
            return $this->errorResponse($result['message'] ?? 'No account officers found.', 404, $result['data']);
        }
    }
}
