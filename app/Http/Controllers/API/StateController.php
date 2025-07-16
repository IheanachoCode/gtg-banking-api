<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class StateController extends Controller
{
    use ApiResponse;

    protected $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * Get all states
     * 
     * Retrieve a list of all states in Nigeria.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "States retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Lagos",
     *       "code": "LA",
     *       "region": "South West"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Kano",
     *       "code": "KN",
     *       "region": "North West"
     *     }
     *   ]
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(): JsonResponse
    {
        $result = $this->stateService->getAllStates();
        
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message']);
        } else {
            return $this->errorResponse($result['message'], 404);
        }
    }
}