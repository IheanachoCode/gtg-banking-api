<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function getTransactionDates(string $accountNo): array
    {
        try {
            $dates = Transaction::where('account_no', $accountNo)
                ->select('transaction_date')
                ->groupBy('transaction_date')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function($transaction) {
                    return ['transaction_date' => $transaction->transaction_date];
                })
                ->toArray();

            return [
                'status' => !empty($dates),
                'message' => !empty($dates) ? 'Transactions fetched successfully.' : 'No transactions found.',
                'data' => $dates
            ];

        } catch (\Exception $e) {
            Log::error('Transaction dates fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch transaction dates.',
                'data' => []
            ];
        }
    }

    public function getLimitedTransactionDates(string $accountNo): array
    {
        try {
            $dates = Transaction::where('account_no', $accountNo)
                ->select('transaction_date')
                ->groupBy('transaction_date')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get()
                ->map(function($transaction) {
                    return ['transaction_date' => $transaction->transaction_date];
                })
                ->toArray();

            return [
                'status' => !empty($dates),
                'message' => !empty($dates) ? 'Transactions fetched successfully.' : 'No transactions found.',
                'data' => $dates
            ];

        } catch (\Exception $e) {
            Log::error('Limited transaction dates fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);

            return [
                'status' => false,
                'message' => 'Failed to fetch limited transaction dates.',
                'data' => []
            ];
        }
    }

    public function getTransactions($accountNo): array
    {
        try {
            $transactions = Transaction::where('account_no', $accountNo)->get();
            return [
                'status' => $transactions->isNotEmpty(),
                'message' => $transactions->isNotEmpty() ? 'Transactions fetched successfully.' : 'No transactions found.',
                'data' => $transactions->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Transactions fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch transactions.',
                'data' => []
            ];
        }
    }
}