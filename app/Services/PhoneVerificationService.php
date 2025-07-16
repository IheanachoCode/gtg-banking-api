<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PhoneVerificationService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }




        public function sendVerificationOtp(string $phoneNumber): array
    {
        try {
            $otp = rand(1000, 9999);

            $otpCreated = Otp::create([
                'otp' => $otp,
                'customer_contact' => $phoneNumber
            ]);

            if (!$otpCreated) {
                return [
                    'success' => false,
                    'message' => 'Please use a valid Phone Number'
                ];
            }

            $message = "Your Phone Number with OTP: " . $otp;
            $smsSent = $this->smsService->sendSms($phoneNumber, $message);

            if ($smsSent) {
                return [
                    'success' => true,
                    'message' => 'OTP sent to your phone.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send OTP SMS.'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Phone OTP send failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);

            return [
                'success' => false,
                'message' => 'Please use a valid Phone Number'
            ];
        }
    }

    public function verifyOtp(string $phone, string $otpCode): array
    {
        try {
            $isValid = DB::table('client_registrations')
                ->where('phone', $phone)
                ->where('one_time_pasword', $otpCode)
                ->exists();
            return [
                'status' => $isValid,
                'message' => $isValid ? 'Verification successful!' : 'Expired or invalid code',
                'data' => null
            ];
        } catch (\Exception $e) {
            Log::error('Phone OTP verification failed', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return [
                'status' => false,
                'message' => 'Expired or invalid code',
                'data' => null
            ];
        }
    }







    // public function sendVerificationOtp(string $phoneNumber): array
    // {
    //     try {
    //         $otp = rand(1000, 9999);

    //         $otpCreated = Otp::create([
    //             'otp' => $otp,
    //             'customer_contact' => $phoneNumber
    //         ]);

    //         if (!$otpCreated) {
    //             return [
    //                 'response' => 'Please use a valid Phone Number',
    //                 'respond' => false
    //             ];
    //         }

    //         $message = "Your Phone Number with OTP: " . $otp;
    //         $smsSent = $this->smsService->sendSms($phoneNumber, $message);

    //         return [
    //             'respond' => $smsSent
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Phone OTP send failed', [
    //             'error' => $e->getMessage(),
    //             'phone' => $phoneNumber
    //         ]);

    //         return [
    //             'response' => 'Please use a valid Phone Number',
    //             'respond' => false
    //         ];
    //     }
    // }



    // public function verifyPhoneOtp(string $phone, string $otpCode): array
    // {
    //     try {
    //         $isValid = DB::table('client_registrations')
    //             ->where('phone', $phone)
    //             ->where('one_time_pasword', $otpCode)
    //             ->exists();

    //         return [
    //             'response' => $isValid ? 'verification Successful!' : 'Expired Or Invalid Code',
    //             'respond' => $isValid
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Phone OTP verification failed', [
    //             'error' => $e->getMessage(),
    //             'phone' => $phone
    //         ]);

    //         return [
    //             'response' => 'Expired Or Invalid Code',
    //             'respond' => false
    //         ];
    //     }
    // }















}
