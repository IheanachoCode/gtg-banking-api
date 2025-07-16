<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionCountService
{
    public function getTransactionCount(string $accountNo, string $transactionDate): array
    {
        try {
            $count = Transaction::where('account_no', $accountNo)
                ->where('transaction_date', $transactionDate)
                ->count();

            return [
                'data' => $count ?: 0,
                'status' => $count > 0
            ];

        } catch (\Exception $e) {
            Log::error('Transaction count fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo,
                'date' => $transactionDate
            ]);

            return [
                'data' => 0,
                'status' => false
            ];
        }
    }

    public function countTransactions($accountNo): array
    {
        try {
            $count = Transaction::where('account_no', $accountNo)->count();
            return [
                'status' => true,
                'message' => 'Transaction count fetched successfully.',
                'data' => ['count' => $count]
            ];
        } catch (\Exception $e) {
            \Log::error('Transaction count fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch transaction count.',
                'data' => null
            ];
        }
    }
}
