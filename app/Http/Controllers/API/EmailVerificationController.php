<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailOtpRequest;
use App\Services\EmailVerificationService;
use App\Http\Requests\VerifyEmailOtpRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    use ApiResponse;
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Send email verification OTP
     * 
     * Send a one-time password to the user's email for verification.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @response 200 {
     *   "status": "success",
     *   "message": "OTP sent to your email."
     * }
     * @response 400 {
     *   "status": "error",
     *   "message": "Please use a valid Email"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendOtp(EmailOtpRequest $request): JsonResponse
    {
        $result = $this->emailVerificationService->sendVerificationOtp($request->email);
        if (!empty($result['success'])) {
            return $this->successResponse([], 'OTP sent to your email.');
        }
        return $this->errorResponse($result['message'] ?? 'Please use a valid Email', 400);
    }

    /**
     * Verify email OTP
     * 
     * Verify the OTP code sent to the user's email.
     * 
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @body otp_code string required The OTP code
     * @response 200 {
     *   "status": true,
     *   "message": "Email verified successfully",
     *   "data": {
     *     "email": "user@example.com",
     *     "verified": true
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verifyOtp(VerifyEmailOtpRequest $request): JsonResponse
    {
        $result = $this->emailVerificationService->verifyEmailOtp(
            $request->email,
            $request->otp_code
        );
        if (!empty($result['status']) && $result['status'] === true) {
            return $this->successResponse($result['data'] ?? [], $result['message'] ?? 'Email verified successfully');
        }
        return $this->errorResponse($result['message'] ?? 'Verification failed', 400, $result['data'] ?? []);
    }
}
