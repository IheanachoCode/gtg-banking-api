<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneOtpRequest;
use App\Services\PhoneVerificationService;
use App\Http\Requests\VerifyPhoneOtpRequest;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;


class PhoneVerificationController extends Controller
{

    use ApiResponse;
    protected $phoneVerificationService;

    public function __construct(PhoneVerificationService $phoneVerificationService)
    {
        $this->phoneVerificationService = $phoneVerificationService;
    }

    /**
     * Send phone verification OTP
     * 
     * Send a one-time password to the user's phone for verification.
     * 
     * @header Authorization string required Bearer token
     * @body phone string required The user's phone number
     * @response 200 {
     *   "status": "success",
     *   "message": "OTP sent to your phone"
     * }
     * @response 400 {
     *   "status": "error",
     *   "message": "Failed to send OTP"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendOtp(PhoneOtpRequest $request): JsonResponse
    {
        $result = $this->phoneVerificationService->sendVerificationOtp($request->phone);

        if ($result['success']) {
            return $this->successResponse(null, $result['message']);
        } else {
            return $this->errorResponse($result['message'], 400);
        }
    }

    /**
     * Verify phone OTP
     * 
     * Verify the OTP code sent to the user's phone.
     * 
     * @header Authorization string required Bearer token
     * @body phone string required The user's phone number
     * @body otp_code string required The OTP code
     * @response 200 {
     *   "status": "success",
     *   "message": "Phone verified successfully"
     * }
     * @response 400 {
     *   "status": "error",
     *   "message": "Invalid OTP code"
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function verifyOtp(VerifyPhoneOtpRequest $request): JsonResponse
    {
        $result = $this->phoneVerificationService->verifyOtp(
            $request->phone,
            $request->otp_code
        );

        if ($result['status']) {
            return $this->successResponse(null, $result['message']);
        } else {
            return $this->errorResponse($result['message'], 400);
        }
    }
}
