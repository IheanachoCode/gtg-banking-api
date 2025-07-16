<?php

namespace App\Services;

use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawalService
{
    public function processWithdrawalRequest(array $data): array
    {
        $accountInfo = DB::table('account_number')
            ->where('account_no', $data['AccountNo'])
            ->first();

        $userInfo = DB::table('client_registrations')
            ->where('user_id', $data['userID'])
            ->first();

        $charges = DB::table('account_constraints')
            ->where('account_type', $accountInfo->account_type)
            ->first();

        if ($userInfo->Pin !== $data['Pin']) {
            return [
                'status' => false,
                'message' => 'Invalid PIN.',
                'data' => null
            ];
        }

        try {
            $withdrawal = WithdrawalRequest::create([
                'Account_no' => $data['AccountNo'],
                'Account_name' => $userInfo->lastname . ' ' . $userInfo->othernames,
                'Account_type' => $accountInfo->account_type,
                'Account_officer' => 'No Staff',
                'Amount' => $data['amount'],
                'description' => $data['description'],
                'commision_charges' => $charges->transactions_charges,
                'user_id' => $data['userID'],
                'Ref_no' => WithdrawalRequest::generateRefNo(),
                'status' => 'Not Verified',
                'Transaction_date' => Carbon::now()->format('Y-m-d'),
                'staff_id' => 'No Staff'
            ]);

            return [
                'status' => (bool) $withdrawal,
                'message' => $withdrawal ? 'Withdrawal request processed successfully.' : 'Failed to process withdrawal request.',
                'data' => $withdrawal ? $withdrawal->toArray() : null
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to process withdrawal request.',
                'data' => null
            ];
        }
    }

    public function createWithdrawalRequest(array $data): array
    {
        try {
            $withdrawal = WithdrawalRequest::create($data);
            return [
                'status' => (bool) $withdrawal,
                'message' => $withdrawal ? 'Withdrawal request created successfully.' : 'Failed to create withdrawal request.',
                'data' => $withdrawal ? $withdrawal->toArray() : null
            ];
        } catch (\Exception $e) {
            \Log::error('Withdrawal request creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to create withdrawal request.',
                'data' => null
            ];
        }
    }
}