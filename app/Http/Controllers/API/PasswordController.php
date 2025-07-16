<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\PasswordRecoveryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;




class PasswordController extends Controller
{

    use ApiResponse;
    protected $passwordService;

    public function __construct(PasswordRecoveryService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * Recover forgotten password
     * 
     * Initiate password recovery process for a user.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @response 200 {
     *   "status": true,
     *   "message": "Password recovery email sent",
     *   "data": {
     *     "email": "user@example.com",
     *     "reset_token": "reset-token-123",
     *     "expires_at": "2024-01-01T11:30:00.000000Z"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->recoverPassword($request->validated());
        return $this->successResponse($result);
    }
}
