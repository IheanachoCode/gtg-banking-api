<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiometricRequest;
use App\Services\BiometricService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BiometricController extends Controller
{
    use ApiResponse;
    protected $biometricService;

    public function __construct(BiometricService $biometricService)
    {
        $this->biometricService = $biometricService;
    }

    /**
     * Fetch user biometric data
     *
     * Retrieve biometric data for a specific user.
     *
     * @header Authorization string required Bearer token
     * @body user_id string required The user's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "Biometric data fetched successfully",
     *   "data": {
     *     "user_id": "user123",
     *     "biometric": {
     *       "fingerprint": "...",
     *       "face_id": "..."
     *     }
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function fetchBiometric(BiometricRequest $request): JsonResponse
    {
        $result = $this->biometricService->getUserBiometric($request->user_id);
        return $this->successResponse($result);
    }
}
