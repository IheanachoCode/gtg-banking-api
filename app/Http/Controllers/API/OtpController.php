<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateOtpRequest;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    use ApiResponse;
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Generate and send OTP
     * 
     * Generate a one-time password and send it to the user's phone.
     * 
     * @header Authorization string required Bearer token
     * @body phone string required The phone number to send OTP to
     * @response 200 {
     *   "status": true,
     *   "message": "OTP sent successfully",
     *   "data": {
     *     "phone": "08012345678",
     *     "otp_expires_in": 300
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function generate(GenerateOtpRequest $request): JsonResponse
    {
        $result = $this->otpService->generateAndSendOtp($request->validated());
        return $this->successResponse($result);
    }

    /**
     * Verify OTP code
     * 
     * Verify the one-time password entered by the user.
     * 
     * @header Authorization string required Bearer token
     * @body phone string required The phone number
     * @body otp string required The OTP code to verify
     * @response 200 {
     *   "status": true,
     *   "message": "OTP verified successfully",
     *   "data": {
     *     "phone": "08012345678",
     *     "verified": true
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->otpService->verifyOtp($request->validated());
        return $this->successResponse($result);
    }
}


