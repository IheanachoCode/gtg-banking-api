<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PhoneSignupService
{
    private $smsConfig;

    public function __construct()
    {
        $this->smsConfig = config('services.kudisms');
    }

    public function sendVerificationOtp(string $phone): array
    {
        try {
            $otp = rand(100000, 999999);
            
            $user = User::where('phone', $phone)->first();
            
            if (!$user || !$this->updateUserOtp($user, $otp)) {
                return [
                    'status' => false,
                    'message' => 'Please use a valid Phone Number',
                    'data' => null
                ];
            }

            $messageBody = "Your Mobile Registration OTP is: " . $otp;
            
            if ($this->sendSms($phone, $messageBody)) {
                return [
                    'status' => true,
                    'message' => 'OTP sent successfully.',
                    'data' => null
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to send OTP',
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('Phone signup verification failed', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'status' => false,
                'message' => 'Please use a valid Phone Number',
                'data' => null
            ];
        }
    }

    private function updateUserOtp(User $user, string $otp): bool
    {
        return $user->update(['one_time_pasword' => $otp]);
    }

    private function sendSms(string $phone, string $message): bool
    {
        try {
            $response = Http::get($this->smsConfig['api_url'], [
                'username' => $this->smsConfig['username'],
                'password' => $this->smsConfig['password'],
                'sender' => $this->smsConfig['sender'],
                'message' => $message,
                'mobiles' => $phone
            ]);

            $result = $response->json();

            if (isset($result['status']) && strtoupper($result['status']) == 'OK') {
                // Store SMS charges if needed
                return true;
            }

            Log::error('SMS sending failed', [
                'error' => $result['error'] ?? 'Unknown error',
                'phone' => $phone
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return false;
        }
    }
}