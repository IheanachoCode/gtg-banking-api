<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    public function verifyOtp(string $otpCode): array
    {
        try {
            $otp = Otp::where('otp', $otpCode)
                ->where('status', Otp::STATUS_UNVERIFIED)
                ->first();

            if (!$otp) {
                return [
                    'message' => 'Expired Or Invalid Code',
                    'status' => false
                ];
            }

            $updated = $otp->update(['status' => Otp::STATUS_VERIFIED]);

            return [
                'message' => $updated ? 'verification Successful!' : 'Update Failed',
                'status' => $updated
            ];

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'otp_code' => $otpCode
            ]);

            return [
                'message' => 'Expired Or Invalid Code',
                'status' => false
            ];
        }
    }
}
