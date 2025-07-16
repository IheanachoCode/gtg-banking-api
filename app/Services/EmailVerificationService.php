<?php

namespace App\Services;

use App\Models\Otp;
use App\Notifications\EmailVerificationOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EmailVerificationService
{
    // public function sendVerificationOtp(string $email): array
    // {
    //     try {
    //         $otp = rand(1000, 9999);

    //         $otpCreated = Otp::create([
    //             'otp' => $otp,
    //             'customer_contact' => $email
    //         ]);

    //         if ($otpCreated) {
    //             Notification::route('mail', $email)
    //                 ->notify(new EmailVerificationOtp($otp));

    //             return [
    //                 'respond' => true
    //             ];
    //         }

    //         return [
    //             'response' => 'Please use a valid Email',
    //             'respond' => false
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Email OTP send failed', [
    //             'error' => $e->getMessage(),
    //             'email' => $email
    //         ]);

    //         return [
    //             'response' => 'Please use a valid Email',
    //             'respond' => false
    //         ];
    //     }
    // }


        public function sendVerificationOtp(string $email): array
    {
        try {
            $otp = rand(1000, 9999);

            $otpCreated = Otp::create([
                'otp' => $otp,
                'customer_contact' => $email
            ]);

            if ($otpCreated) {
                Notification::route('mail', $email)
                    ->notify(new EmailVerificationOtp($otp));

                return [
                    'success' => true,
                    'message' => 'OTP sent to your email.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Please use a valid Email'
            ];

        } catch (\Exception $e) {
            Log::error('Email OTP send failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return [
                'success' => false,
                'message' => 'Please use a valid Email'
            ];
        }
    }


    // public function verifyEmailOtp(string $email, string $otpCode): array
    // {
    //     try {
    //         $isValid = DB::table('client_registrations')
    //             ->where('Email', $email)
    //             ->where('one_time_pasword', $otpCode)
    //             ->exists();

    //         return [
    //             'response' => $isValid ? 'verification Successful!' : 'Expired Or Invalid Code',
    //             'respond' => $isValid
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Email OTP verification failed', [
    //             'error' => $e->getMessage(),
    //             'email' => $email
    //         ]);

    //         return [
    //             'response' => 'Expired Or Invalid Code',
    //             'respond' => false
    //         ];
    //     }
    // }


        public function verifyEmailOtp(string $email, string $otpCode): array
    {
        try {
            $isValid = DB::table('client_registrations')
                ->where('Email', $email)
                ->where('one_time_pasword', $otpCode)
                ->exists();

                

            return [
                'status' => $isValid,
                'message' => $isValid ? 'Verification successful!' : 'Expired or invalid code'
            ];

        } catch (\Exception $e) {
            Log::error('Email OTP verification failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return [
                'status' => false,
                'message' => 'Expired or invalid code'
            ];
        }
    }



}
