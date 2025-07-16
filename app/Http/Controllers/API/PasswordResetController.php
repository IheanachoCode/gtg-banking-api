<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordOtpRequest;
use App\Services\PasswordResetService;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\CreateNewPasswordRequest;
use Illuminate\Http\JsonResponse;


class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Send password reset OTP
     * 
     * Send a one-time password for password reset.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @response 200 {
     *   "status": true,
     *   "message": "OTP sent successfully",
     *   "data": {
     *     "email": "user@example.com",
     *     "otp_expires_in": 300
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendOtp(ForgetPasswordOtpRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->sendOtp($request->validated());
        return response()->json($result);
    }

    /**
     * Verify password reset OTP
     * 
     * Verify the OTP code for password reset.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @body otp string required The OTP code
     * @response 200 {
     *   "status": true,
     *   "message": "OTP verified successfully",
     *   "data": {
     *     "email": "user@example.com",
     *     "verified": true
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->verifyOtp($request->validated());
        return response()->json($result);
    }

    /**
     * Create new password
     * 
     * Create a new password after OTP verification.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @body otp_code string required The verified OTP code
     * @body password string required The new password
     * @body password_confirmation string required Password confirmation
     * @response 200 {
     *   "status": true,
     *   "message": "Password updated successfully",
     *   "data": {
     *     "email": "user@example.com",
     *     "updated_at": "2024-01-01T10:30:00.000000Z"
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function createNewPassword(CreateNewPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordResetService->createNewPassword($request->validated());
        return response()->json($result);
    }
}
