<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function getAccountHolderName(string $accountNo): string
    {
        $userId = DB::table('client_deposit_withdrawal')
            ->where('account_no', $accountNo)
            ->value('user_id');

        if (!$userId) {
            throw new \Exception('User ID not found for account');
        }

        $user = DB::table('client_registrations')
            ->where('user_id', $userId)
            ->select(['lastname', 'othernames'])
            ->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        return trim($user->lastname . ' ' . $user->othernames);
    }


    public function getAccountType(string $accountNo): array
    {
        try {
            $accountType = Account::where('account_no', $accountNo)
                ->value('account_type');
            return [
                'status' => (bool) $accountType,
                'message' => $accountType ? 'Account type fetched successfully.' : 'Account type not found.',
                'data' => $accountType ? ['account_type' => $accountType] : null
            ];
        } catch (\Exception $e) {
            Log::error('Account type fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch account type.',
                'data' => null
            ];
        }
    }



    public function getAccountStatus(string $accountNo): array
    {
        try {
            $accountStatus = Account::where('account_no', $accountNo)
                ->value('account_status');
            return [
                'status' => (bool) $accountStatus,
                'message' => $accountStatus ? 'Account status fetched successfully.' : 'Account status not found.',
                'data' => $accountStatus ? ['account_status' => $accountStatus] : null
            ];
        } catch (\Exception $e) {
            Log::error('Account status fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch account status.',
                'data' => null
            ];
        }
    }



        public function getAccountNumber(string $userId, string $password): array
    {
        try {
            $user = User::where('user_id', $userId)->first();
            if (!$user || !Hash::check($password, $user->password)) {
                return [
                    'status' => false,
                    'message' => 'Wrong Userid OR Password',
                    'data' => null
                ];
            }
            $accountNumber = $user->account?->account_no;
            return [
                'status' => (bool) $accountNumber,
                'message' => $accountNumber ? 'Account number fetched successfully.' : 'Account number not found.',
                'data' => $accountNumber ? ['account_no' => $accountNumber] : null
            ];
        } catch (\Exception $e) {
            Log::error('Account number fetch failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch account number.',
                'data' => null
            ];
        }
    }



    // public function getAccountNumber(string $userId, string $password): array
    // {
    //     try {
    //         $user = User::where('user_id', $userId)
    //             ->where('password', $password)
    //             ->first();

    //         if (!$user) {
    //             return [
    //                 'data' => 'Wrong Userid OR Password',
    //                 'status' => false
    //             ];
    //         }

    //         $accountNumber = $user->account?->account_no;

    //         return [
    //             'data' => $accountNumber ?: 'Failed',
    //             'status' => (bool) $accountNumber
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Account number fetch failed', [
    //             'error' => $e->getMessage(),
    //             'user_id' => $userId
    //         ]);

    //         return [
    //             'data' => 'Failed',
    //             'status' => false
    //         ];
    //     }
    // }

}
