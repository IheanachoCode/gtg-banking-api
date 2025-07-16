<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupEmailRequest;
use App\Services\SignupVerificationService;
use Illuminate\Http\JsonResponse;

class SignupVerificationController extends Controller
{
    protected $signupVerificationService;

    public function __construct(SignupVerificationService $signupVerificationService)
    {
        $this->signupVerificationService = $signupVerificationService;
    }

    /**
     * Send signup email verification OTP
     *
     * Send a one-time password to the user's email for signup verification.
     *
     * @header Authorization string required Bearer token
     * @body email string required The user's email address
     * @response 200 {
     *   "status": true,
     *   "message": "OTP sent to your email for signup verification",
     *   "data": {
     *     "email": "user@example.com",
     *     "otp_expires_in": 300
     *   }
     * }
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function sendVerificationEmail(SignupEmailRequest $request): JsonResponse
    {
        $result = $this->signupVerificationService->sendVerificationOtp($request->email);
        return response()->json($result);
    }
}
