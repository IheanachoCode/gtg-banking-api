<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PinManagementService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function changePin(array $data): array
    {
        if (!$this->validateUserCredentials($data)) {
            return [
                'response' => 'One or More Invalid Input',
                'respond' => false
            ];
        }

        if (!$this->validateSecurityQuestion($data)) {
            return [
                'response' => 'Incorrect Question Or Answer',
                'respond' => false
            ];
        }

        if (!$this->validateOldPin($data)) {
            return [
                'response' => 'Old Pin Not Correct',
                'respond' => false
            ];
        }

        $updated = $this->updatePin($data['phone_number'], $data['new_pin']);

        if ($updated) {
            $this->smsService->sendSms(
                $data['phone_number'],
                'Your New Transaction Pin Updated Successfully'
            );

            return [
                'response' => 'successful',
                'respond' => true
            ];
        }

        return [
            'response' => 'Failed',
            'respond' => false
        ];
    }

    protected function validateUserCredentials(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->exists() &&
            DB::table('account_number')
            ->where('account_no', $data['account_no'])
            ->exists();
    }

    protected function validateSecurityQuestion(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->where('secret_question', $data['secret_question'])
            ->where('secret_answer', $data['secret_answer'])
            ->exists();
    }

    protected function validateOldPin(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('Pin', $data['oldPin'])
            ->exists();
    }

    protected function updatePin(string $phoneNumber, string $newPin): bool
    {
        try {
            return DB::table('client_registrations')
                ->where('phone', $phoneNumber)
                ->update(['Pin' => $newPin]) > 0;
        } catch (\Exception $e) {
            Log::error('PIN update failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            return false;
        }
    }
}