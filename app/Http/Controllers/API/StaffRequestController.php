<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequestFormRequest;
use App\Services\StaffRequestService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

class StaffRequestController extends Controller
{
    use ApiResponse;

    protected $requestService;

    public function __construct(StaffRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

/**
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function store(StaffRequestFormRequest $request)
    {
        try {
            $staffRequest = $this->requestService->createRequest($request->validated());

            return $this->successResponse([
                'respond' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Staff request creation error', [
                'error' => $e->getMessage(),
                'staffID' => $request->staffID
            ]);

            return $this->successResponse([
                'respond' => false
            ]);
        }
    }
}