<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupPhoneRequest;
use App\Services\PhoneSignupService;
use Illuminate\Http\JsonResponse;

class PhoneSignupController extends Controller
{
    protected $phoneSignupService;

    public function __construct(PhoneSignupService $phoneSignupService)
    {
        $this->phoneSignupService = $phoneSignupService;
    }

    /**
     * Send signup phone verification OTP
     *
     * Send a one-time password to the user's phone for signup verification.
     *
     * @header Authorization string required Bearer token
     * @body phone string required The user's phone number
     * @response 200 {
     *   "status": true,
     *   "message": "OTP sent to your phone for signup verification",
     *   "data": {
     *     "phone": "08012345678",
     *     "otp_expires_in": 300
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendVerificationOtp(SignupPhoneRequest $request): JsonResponse
    {
        $result = $this->phoneSignupService->sendVerificationOtp($request->phone);
        return response()->json($result);
    }
}