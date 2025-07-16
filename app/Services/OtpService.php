<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function generateAndSendOtp(array $data): array
    {
        try {
            if (!$this->validateUser($data)) {
                return [
                    'status' => false,
                    'message' => 'User ID does not exist',
                    'data' => null
                ];
            }
            $otp = $this->generateOtp();
            $updated = $this->storeOtp($data['user_id'], $otp);
            if (!$updated) {
                return [
                    'status' => false,
                    'message' => 'Failed to store OTP',
                    'data' => null
                ];
            }
            $message = "Your One Time Password is: " . $otp;
            $this->smsService->sendSms($data['phone'], $message);
            return [
                'status' => true,
                'message' => 'OTP generated and sent successfully.',
                'data' => null
            ];
        } catch (\Exception $e) {
            Log::error('OTP generation failed', [
                'error' => $e->getMessage(),
                'user_id' => $data['user_id']
            ]);
            return [
                'status' => false,
                'message' => 'Failed to generate OTP',
                'data' => null
            ];
        }
    }

    private function validateUser(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $data['user_id'])
            ->where('phone', $data['phone'])
            ->exists();
    }

    private function generateOtp(): string
    {
        return (string) rand(1000, 9999);
    }

    private function storeOtp(string $userId, string $otp): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->update(['one_time_pasword' => $otp]) > 0;
    }

    public function verifyOtp(array $data): array
    {
        try {
            // Check for required inputs
            if (empty($data['user_id']) || empty($data['otp_code'])) {
                Log::warning('OTP verification failed: missing input', [
                    'input' => $data
                ]);
                return [
                    'response' => 'Missing user ID or OTP code.',
                    'respond' => false
                ];
            }

            // Validate OTP
            if (!$this->validateOtp($data['user_id'], $data['otp_code'])) {
                Log::info('OTP verification failed: invalid code', [
                    'user_id' => $data['user_id'],
                    'otp_code' => $data['otp_code']
                ]);
                return [
                    'response' => 'Invalid OTP code.',
                    'respond' => false
                ];
            }

            // Clear OTP
            $cleared = $this->clearOtp($data['user_id']);

            if ($cleared) {
                Log::info('OTP verified and cleared', [
                    'user_id' => $data['user_id']
                ]);
                return [
                    'response' => 'OTP verified successfully.',
                    'respond' => true
                ];
            } else {
                Log::warning('OTP verified but failed to clear', [
                    'user_id' => $data['user_id']
                ]);
                return [
                    'response' => 'OTP verified, but failed to clear OTP.',
                    'respond' => false
                ];
            }

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'user_id' => $data['user_id'] ?? null
            ]);

            return [
                'response' => 'An unexpected error occurred. Please try again.',
                'respond' => false
            ];
        }
    }

    private function validateOtp(string $userId, string $otpCode): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->where('one_time_pasword', $otpCode)
            ->exists();
    }

    private function clearOtp(string $userId): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->update(['one_time_pasword' => null]) > 0;
    }
}
