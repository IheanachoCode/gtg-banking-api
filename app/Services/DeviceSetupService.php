<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeviceSetupService
{
    public function setupDevice(array $data): array
    {
        switch ($data['setup_type']) {
            case 'New':
                return $this->handleNewDeviceSetup($data);
            case 'Transfer':
            case 'Lost Device':
                return $this->handleDeviceTransfer($data);
            default:
                return [
                    'data' => 'Failed',
                    'status' => false
                ];
        }
    }

    private function handleNewDeviceSetup(array $data): array
    {
        if (!$this->validateUserAndAccount($data)) {
            return [
                'data' => 'One or More Invalid Input',
                'status' => false
            ];
        }

        if (!$this->isDeviceNotLinked($data['user_id'])) {
            return [
                'data' => 'User Account Already Linked With a Device',
                'status' => false
            ];
        }

        $success = $this->updateUserDevice($data);
        
        return [
            'data' => $success ? 'successful' : 'Failed',
            'status' => $success
        ];
    }

    private function handleDeviceTransfer(array $data): array
    {
        if (!$this->validateUserAccountAndPassword($data)) {
            return [
                'data' => 'One or More Invalid Input',
                'status' => false
            ];
        }

        if ($this->isDeviceNotLinked($data['user_id'])) {
            return [
                'data' => 'User Account Not Linked to any Device!!!',
                'status' => false
            ];
        }

        $success = $this->updateUserDevice($data);

        return [
            'data' => $success ? 'successful' : 'Failed',
            'status' => $success
        ];
    }

    private function validateUserAndAccount(array $data): bool
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

    private function validateUserAccountAndPassword(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $data['user_id'])
            ->where('phone', $data['phone_number'])
            ->where('password', $data['password'])
            ->exists() &&
            DB::table('account_number')
            ->where('user_id', $data['user_id'])
            ->where('account_no', $data['account_no'])
            ->exists();
    }

    private function isDeviceNotLinked(string $userId): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->whereNull('device_series_no')
            ->orWhere('device_series_no', '')
            ->exists();
    }

    private function updateUserDevice(array $data): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $data['user_id'])
            ->update([
                'device_series_no' => $data['device_series_no'],
                'secret_question' => $data['secret_question'],
                'secret_answer' => $data['secret_answer'],
                'password' => $data['password']
            ]) > 0;
    }



    /**
 * Updates device information for a given user
 * 
 * @param string $deviceSeriesNo
 * @param string $secretQuestion
 * @param string $secretAnswer
 * @param string $userId
 * @return bool
 */
// private function updateUserDevice(array $data): bool 
// {
//     try {
//         return DB::table('client_registrations')
//             ->where('user_id', $data['user_id'])
//             ->update([
//                 'device_series_no' => $data['device_series_no'],
//                 'secret_question' => $data['secret_question'],
//                 'secret_answer' => $data['secret_answer']
//             ]) > 0;
//     } catch (\Exception $e) {
//         Log::error('Device update failed', [
//             'error' => $e->getMessage(),
//             'user_id' => $data['user_id']
//         ]);
//         return false;
//     }
// }













}