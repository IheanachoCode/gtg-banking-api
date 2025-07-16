<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;

class PasswordRecoveryService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // public function recoverPassword(array $data): array
    // {
    //     if (!$this->validateUserCredentials($data)) {
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

    //     $password = $this->getUserPassword($data['user_id']);

    //     if (empty($password)) {
    //         return [
    //             'response' => 'Failed',
    //             'respond' => false
    //         ];
    //     }

    //     $messageBody = "Your Login Password is: " . $password;
    //     $smsSent = $this->smsService->sendSms($data['phone_number'], $messageBody);

    //     return [
    //         'response' => $smsSent ? 'successful' : 'Failed',
    //         'respond' => $smsSent
    //     ];
    // }


    public function recoverPassword(array $data): array
    {
        if (!$this->validateUserCredentials($data)) {
            return [
                'status' => false,
                'message' => 'One or More Invalid Input',
                'data' => null
            ];
        }
        if (!$this->validateSecurityQuestion($data)) {
            return [
                'status' => false,
                'message' => 'Incorrect Question Or Answer',
                'data' => null
            ];
        }
        $password = $this->getUserPassword($data['user_id']);
        if (empty($password)) {
            return [
                'status' => false,
                'message' => 'Failed to retrieve password.',
                'data' => null
            ];
        }
        $messageBody = "Your Login Password is: " . $password;
        $smsSent = false;
        try {
            $smsSent = $this->smsService->sendSms($data['phone_number'], $messageBody);
        } catch (\Exception $e) {
            \Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'phone' => $data['phone_number']
            ]);
        }
        return [
            'status' => true,
            'message' => 'Password recovery successful' . ($smsSent ? ' (SMS sent)' : ' (SMS not sent)'),
            'data' => ['sms_sent' => $smsSent]
        ];
    }



    protected function validateUserCredentials(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $data['user_id'])
            ->where('phone', $data['phone_number'])
            ->exists() &&
            DB::table('account_number')
            ->where('user_id', $data['user_id'])
            ->where('account_no', $data['account_no'])
            ->exists();
    }

    protected function validateSecurityQuestion(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $data['user_id'])
            ->where('secret_question', $data['secret_question'])
            ->where('secret_answer', $data['secret_answer'])
            ->exists();
    }

    protected function getUserPassword(string $userId): ?string
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->value('password');
    }
}
