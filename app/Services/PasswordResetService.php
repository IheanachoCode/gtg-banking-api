<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendOtp(array $data): array
    {
        try {
            $otp = rand(1000, 9999);
            $contactType = $data['contact_type'];
            $contact = $data[$contactType];

            if (!$this->validateContact($contactType, $contact)) {
                return [
                    'message' => "Contact_details don't Exist",
                    'status' => false
                ];
            }

            if ($contactType === 'phone') {
                return $this->handlePhoneOtp($contact, $otp);
            } else {
                return $this->handleEmailOtp($contact, $otp);
            }

        } catch (\Exception $e) {
            Log::error('OTP send failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'status' => false
            ];
        }
    }

    private function validateContact(string $type, string $value): bool
    {
        return User::where($type, $value)->exists();
    }

    private function handlePhoneOtp(string $phone, string $otp): array
    {
        $message = "Your One Time Reset Password No is: " . $otp;

        $updated = User::where('phone', $phone)
            ->update(['one_time_pasword' => $otp]);

        if ($updated) {
            $this->smsService->sendSms($phone, $message);
            return ['status' => true];
        }

        return ['status' => false];
    }

    private function handleEmailOtp(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['one_time_pasword' => $otp]);
            $user->notify(new OtpNotification($otp));
            return ['status' => true];
        }

        return ['status' => false];
    }

    public function verifyOtp(array $data): array
    {
        try {
            $contactType = $data['contact_type'];
            $contact = $data[$contactType];
            $otp = $data['otp_code'];

            $isValid = $this->validateOtp($contactType, $contact, $otp);

            return [
                'message' => $isValid ? 'verification Successful!' : 'Invalid Code',
                'status' => $isValid
            ];

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'message' => 'Invalid Code',
                'status' => false
            ];
        }
    }

    private function validateOtp(string $type, string $contact, string $otp): bool
    {
        return DB::table('client_registrations')
            ->where($type, $contact)
            ->where('one_time_pasword', $otp)
            ->exists();
    }


    public function createNewPassword(array $data): array
    {
        try {
            $contactType = $data['contact_type'];
            $contact = $data[$contactType];
            $password = Hash::make($data['new_password']);

            $updated = $this->updatePassword($contactType, $contact, $password);

            return [
                'message' => $updated ? 'Reset Successful' : 'Unable to process',
                'status' => $updated
            ];

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'contact_type' => $data['contact_type']
            ]);

            return [
                'message' => 'Unable to process',
                'status' => false
            ];
        }
    }

    private function updatePassword(string $type, string $contact, string $password): bool
    {
        return DB::table('client_registrations')
            ->where($type, $contact)
            ->update(['password' => $password]) > 0;
    }


}
