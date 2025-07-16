<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchLgaRequest;
use App\Services\LgaService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;


class LgaController extends Controller
{
    use ApiResponse;
    
    protected $lgaService;

    public function __construct(LgaService $lgaService)
    {
        $this->lgaService = $lgaService;
    }

    /**
     * Get LGAs by state
     * 
     * Retrieve all Local Government Areas (LGAs) for a specific state.
     * 
     * @header Authorization string required Bearer token
     * @body state string required The state name to get LGAs for
     * @response 200 {
     *   "status": true,
     *   "message": "LGAs retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ikeja",
     *       "state": "Lagos",
     *       "code": "IKE"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Victoria Island",
     *       "state": "Lagos",
     *       "code": "VI"
     *     }
     *   ]
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "No LGAs found for this state",
     *   "data": []
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function index(FetchLgaRequest $request): JsonResponse
    {
        $result = $this->lgaService->getLgasByState($request->state);
        
        if ($result['status']) {
            return $this->successResponse($result['data'], $result['message']);
        } else {
            return $this->errorResponse($result['message'], 404);
        }
    }
}