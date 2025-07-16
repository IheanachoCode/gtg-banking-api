<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FingerprintValidationRequest;
use App\Services\FingerprintService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{

    use ApiResponse;
    protected $fingerprintService;

    public function __construct(FingerprintService $fingerprintService)
    {
        $this->fingerprintService = $fingerprintService;
    }

    /**
     * Validate and enable fingerprint
     * 
     * Validate user's fingerprint and enable fingerprint authentication.
     * 
     * @header Authorization string required Bearer token
     * @body user_id string required The user's unique identifier
     * @body fingerprint_data string required The fingerprint data
     * @response 200 {
     *   "status": true,
     *   "message": "Fingerprint validated successfully",
     *   "data": {
     *     "user_id": "user123",
     *     "fingerprint_enabled": true,
     *     "validated_at": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function validateFingerprint(FingerprintValidationRequest $request): JsonResponse
    {
        $result = $this->fingerprintService->validateAndEnableFingerprint($request->validated());
        return $this->successResponse($result);
    }
}
