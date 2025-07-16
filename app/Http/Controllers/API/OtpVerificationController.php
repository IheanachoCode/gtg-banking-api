<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyOtpCheckRequest;
use App\Services\OtpVerificationService;
use Illuminate\Http\JsonResponse;


class OtpVerificationController extends Controller
{
    protected $otpVerificationService;

    public function __construct(OtpVerificationService $otpVerificationService)
    {
        $this->otpVerificationService = $otpVerificationService;
    }

    /**
     * Verify OTP code
     *
     * Verify a one-time password code for user authentication.
     *
     * @header Authorization string required Bearer token
     * @body otp_code string required The OTP code to verify
     * @response 200 {
     *   "status": true,
     *   "message": "OTP verified successfully",
     *   "data": {
     *     "verified": true
     *   }
     * }
     * @response 400 {
     *   "status": false,
     *   "message": "Invalid OTP code",
     *   "data": null
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verifyOtp(VerifyOtpCheckRequest $request): JsonResponse
    {
        $result = $this->otpVerificationService->verifyOtp($request->otp_code);
        return response()->json($result);
    }
}