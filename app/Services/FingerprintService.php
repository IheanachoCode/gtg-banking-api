<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FingerprintService
{
    public function validateAndEnableFingerprint(array $data): array
    {
        try {
            // Check for required inputs
            $required = ['phone_number', 'account_no', 'secret_question', 'secret_answer'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    Log::warning('Fingerprint enable failed: missing input', [
                        'missing_field' => $field,
                        'input' => $data
                    ]);
                    return [
                        'status' => false,
                        'message' => "Missing required field: $field.",
                        'data' => null
                    ];
                }
            }

            // Validate credentials
            if (!$this->validateCredentials($data)) {
                Log::info('Fingerprint enable failed: invalid credentials', [
                    'phone_number' => $data['phone_number'],
                    'account_no' => $data['account_no']
                ]);
                return [
                    'status' => false,
                    'message' => 'Invalid phone number or account number.',
                    'data' => null
                ];
            }

            // Validate security question/answer
            if (!$this->validateSecurityQuestion($data)) {
                Log::info('Fingerprint enable failed: invalid security question/answer', [
                    'phone_number' => $data['phone_number'],
                    'secret_question' => $data['secret_question'],
                    'secret_answer' => $data['secret_answer']
                ]);
                return [
                    'status' => false,
                    'message' => 'Incorrect security question or answer.',
                    'data' => null
                ];
            }

            // Enable fingerprint
            $success = $this->enableFingerprint($data['phone_number']);

            if ($success) {
                Log::info('Fingerprint enabled successfully', [
                    'phone_number' => $data['phone_number']
                ]);
                return [
                    'status' => true,
                    'message' => 'Fingerprint enabled successfully.',
                    'data' => null
                ];
            } else {
                Log::warning('Fingerprint enable failed: update failed', [
                    'phone_number' => $data['phone_number']
                ]);
                return [
                    'status' => false,
                    'message' => 'Failed to enable fingerprint. Please try again later.',
                    'data' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Fingerprint validation failed', [
                'error' => $e->getMessage(),
                'input' => $data
            ]);
            return [
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again.',
                'data' => null
            ];
        }
    }

    private function validateCredentials(array $data): bool
    {
        $phoneExists = DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->exists();

        $accountExists = DB::table('account_number')
            ->where('account_no', $data['account_no'])
            ->exists();

        return $phoneExists && $accountExists;
    }

    private function validateSecurityQuestion(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('phone', $data['phone_number'])
            ->where('secret_question', $data['secret_question'])
            ->where('secret_answer', $data['secret_answer'])
            ->exists();
    }

    // private function enableFingerprint(string $phoneNumber): bool
    // {
    //     return DB::table('client_registrations')
    //         ->where('phone', $phoneNumber)
    //         ->update(['finger_print' => 'YES']) > 0;
    // }


    private function enableFingerprint(string $phoneNumber): bool
{
    $user = DB::table('client_registrations')
        ->where('phone', $phoneNumber)
        ->first();

    if (!$user) {
        return false;
    }

    if ($user->finger_print === 'YES') {
        // Already enabled
        return true;
    }

    return DB::table('client_registrations')
        ->where('phone', $phoneNumber)
        ->update(['finger_print' => 'YES']) > 0;
}

}
