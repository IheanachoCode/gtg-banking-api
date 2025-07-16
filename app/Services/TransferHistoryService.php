<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransferHistoryService
{
    public function getHistory(string $accountNo, string $transactionDate): array
    {
        try {
            $history = Transaction::where('account_no', $accountNo)
                ->where('transaction_date', $transactionDate)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'amount' => $transaction->amount,
                        'transaction_id' => $transaction->transaction_id,
                        'transaction_type' => $transaction->transaction_type,
                        'from' => $transaction->transact_from,
                        'to' => $transaction->transact_to,
                        'description' => $transaction->description,
                        'type' => $transaction->payment_mode,
                        'transaction_date' => $transaction->transaction_date,
                        'date_time' => $transaction->created_at,
                        'time' => $transaction->time
                    ];
                })
                ->toArray();

            return [
                'data' => !empty($history) ? $history : 'Failed',
                'status' => !empty($history)
            ];

        } catch (\Exception $e) {
            Log::error('Transfer history fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo,
                'date' => $transactionDate
            ]);

            return [
                'data' => 'Failed',
                'status' => false
            ];
        }
    }



        public function getHistoryBetweenDates(string $accountNo, string $startDate, string $endDate): array
    {
        try {
            $history = Transaction::where('account_no', $accountNo)
                ->betweenDates($startDate, $endDate)
                ->orderBy('transaction_date', 'desc')
                ->get()
                ->map(function($transaction) {
                    return [
                        'amount' => $transaction->amount,
                        'transaction_type' => $transaction->transaction_type,
                        'description' => $transaction->description
                    ];
                })
                ->toArray();
            return [
                'status' => !empty($history),
                'message' => !empty($history) ? 'Transfer history fetched successfully.' : 'No transfer history found.',
                'data' => $history
            ];
        } catch (\Exception $e) {
            Log::error('Transfer history between dates fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch transfer history.',
                'data' => []
            ];
        }
    }


    //     public function getHistoryBetweenDates(string $accountNo, string $startDate, string $endDate): array
    // {
    //     try {
    //         $history = Transaction::where('account_no', $accountNo)
    //             ->betweenDates($startDate, $endDate)
    //             ->groupBy('transaction_date')
    //             ->orderBy('id', 'desc')
    //             ->get()
    //             ->map(function($transaction) {
    //                 return [
    //                     'amount' => $transaction->amount,
    //                     'transaction_type' => $transaction->transaction_type,
    //                     'description' => $transaction->description
    //                 ];
    //             })
    //             ->toArray();

    //         return [
    //             'data' => !empty($history) ? $history : 'Failed',
    //             'status' => !empty($history)
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error('Transfer history between dates fetch failed', [
    //             'error' => $e->getMessage(),
    //             'account_no' => $accountNo,
    //             'start_date' => $startDate,
    //             'end_date' => $endDate
    //         ]);

    //         return [
    //             'data' => 'Failed',
    //             'status' => false
    //         ];
    //     }
    // }





























}