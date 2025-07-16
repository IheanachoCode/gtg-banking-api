<?php
namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    public function calculateBalances(string $userId, string $accountNo, string $fromDate, string $toDate): array
    {
        try {
            $credit = Transaction::where([
                'user_id' => $userId,
                'account_no' => $accountNo,
                'transaction_type' => 'Credit'
            ])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

            $debit = Transaction::where([
                'user_id' => $userId,
                'account_no' => $accountNo,
                'transaction_type' => 'Debit'
            ])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

            $verifiedCredit = Transaction::where([
                'user_id' => $userId,
                'account_no' => $accountNo,
                'transaction_type' => 'Credit',
                'verify_stat' => 'Verified'
            ])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

            $verifiedDebit = Transaction::where([
                'user_id' => $userId,
                'account_no' => $accountNo,
                'transaction_type' => 'Debit',
                'verify_stat' => 'Verified'
            ])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

            return [
                'status' => true,
                'message' => 'Balances calculated successfully.',
                'data' => [
                    'credit' => $credit,
                    'debit' => $debit,
                    'availableBalance' => $verifiedCredit - $verifiedDebit,
                    'ledgerBalance' => $credit - $debit,
                    'initial_balance' => $credit
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Balance calculation failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'account_no' => $accountNo
            ]);

            return [
                'status' => false,
                'message' => 'Failed to calculate balances.',
                'data' => []
            ];
        }
    }
}