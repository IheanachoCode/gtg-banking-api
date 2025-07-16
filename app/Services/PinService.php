<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PinService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // public function changePin(array $data): array
    // {
    //     if (!$this->validateCredentials($data)) {
    //         return [
    //             'response' => 'One or More Invalid Input',
    //             'respond' => false
    //         ];
    //     }

    //     if (!$this->validateSecurityQuestion($data)) {
    //         return [
    //             'response' => 'Incorrect Question Or Answer',
    //             'respond' => false
    //         ];
    //     }

    //     if (!$this->validateOldPin($data['oldPin'])) {
    //         return [
    //             'response' => 'Old Pin Not Correct',
    //             'respond' => false
    //         ];
    //     }

    //     $updated = $this->updatePin($data['phone_number'], $data['new_pin']);

    //     if ($updated) {
    //         $this->smsService->sendSms(
    //             $data['phone_number'],
    //             'Your New Transaction Pin Updated Successfully'
    //         );

    //         return [
    //             'response' => 'successful',
    //             'respond' => true
    //         ];
    //     }

    //     return [
    //         'response' => 'Failed',
    //         'respond' => false
    //     ];
    // }


        public function changePin(array $data): array
    {
        if (!$this->validateCredentials($data)) {
            return [
                'response' => 'One or more input fields are invalid.',
                'respond' => false
            ];
        }

        if (!$this->validateSecurityQuestion($data)) {
            return [
                'response' => 'Incorrect security question or answer.',
                'respond' => false
            ];
        }

        if (!$this->validateOldPin($data['oldPin'])) {
            return [
                'response' => 'Old PIN is not correct.',
                'respond' => false
            ];
        }

        $updated = $this->updatePin($data['phone_number'], $data['new_pin']);

        if ($updated) {
            $smsSent = false;
            try {
                $smsSent = $this->smsService->sendSms(
                    $data['phone_number'],
                    'Your new transaction PIN has been updated successfully.'
                );
            } catch (\Exception $e) {
                Log::error('SMS sending failed', [
                    'error' => $e->getMessage(),
                    'phone' => $data['phone_number']
                ]);
            }

            return [
                'response' => $smsSent
                    ? 'PIN changed successfully. SMS sent.'
                    : 'PIN changed successfully, but SMS notification failed.',
                'respond' => true,
                'sms_sent' => $smsSent
            ];
        }

        return [
            'response' => 'Failed to update PIN. Please try again later.',
            'respond' => false
        ];
    }





    private function validateCredentials(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->exists() &&
            DB::table('account_number')
            ->where('account_no', $data['account_no'])
            ->exists();
    }

    private function validateSecurityQuestion(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->where('secret_question', $data['secret_question'])
            ->where('secret_answer', $data['secret_answer'])
            ->exists();
    }

    private function validateOldPin(string $oldPin): bool
    {
        return DB::table('client_registrations')
            ->where('Pin', $oldPin)
            ->exists();
    }

    private function updatePin(string $phone, string $pin): bool
    {
        try {
            return DB::table('client_registrations')
                ->where('phone', $phone)
                ->update(['Pin' => $pin]) > 0;
        } catch (\Exception $e) {
            Log::error('PIN update failed', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return false;
        }
    }



public function validatePin(string $userId, string $pin): bool
{
    // Try to find the user in client_registrations by phone or account_no
    $user = DB::table('client_registrations')
        ->where('user_id', $userId)
        ->orWhere('user_id', $userId)
        ->first();

    if (!$user) {
        return false;
    }

    // If PIN is stored in plain text (not recommended)
    return $user->Pin === $pin;

    // If PIN is hashed, use:
    // return \Hash::check($pin, $user->Pin);
}

}
