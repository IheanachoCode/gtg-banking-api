<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SignupEmailVerification;
use Illuminate\Support\Facades\Log;

class SignupVerificationService
{
    public function sendVerificationOtp(string $email): array
    {
        try {
            $otp = rand(100000, 999999);
            
            $user = User::where('Email', $email)->first();
            
            if (!$user) {
                return [
                    'message' => 'Please use a valid Email',
                    'status' => false
                ];
            }

            $updated = $user->update(['one_time_pasword' => $otp]);

            if ($updated) {
                $user->notify(new SignupEmailVerification($otp));
                return ['status' => true];
            }

            return [
                'message' => 'Please use a valid Email',
                'status' => false
            ];

        } catch (\Exception $e) {
            Log::error('Signup email verification failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return [
                'message' => 'Please use a valid Email',
                'status' => false
            ];
        }
    }
}