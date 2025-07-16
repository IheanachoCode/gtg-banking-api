<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StatementService
{
    // public function getAccountStatement(string $accountNo, string $fromDate, string $toDate): array
    // {
    //     return DB::table('client_deposit_withdrawal')
    //         ->where('account_no', $accountNo)
    //         ->whereBetween('transaction_date', [$fromDate, $toDate])
    //         ->select([
    //             'account_no',
    //             'amount',
    //             'transaction_type',
    //             'transaction_id',
    //             'transaction_date',
    //             'description'
    //         ])
    //         ->orderBy('transaction_date', 'asc')
    //         ->get()
    //         ->map(function ($transaction) {
    //             return [
    //                 'account_no' => $transaction->account_no,
    //                 'amount' => number_format($transaction->amount, 2),
    //                 'transaction_type' => $transaction->transaction_type,
    //                 'transaction_id' => $transaction->transaction_id,
    //                 'transaction_date' => $transaction->transaction_date,
    //                 'description' => $transaction->description
    //             ];
    //         })
    //         ->toArray();
    // }


    
    public function getAccountStatement(string $accountNo, string $fromDate, string $toDate): array
    {
        $transactions = DB::table('client_deposit_withdrawal')
            ->where('account_no', $accountNo)
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'transaction_id',
                'transaction_date',
                'description',
                'current_balance'
            ])
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalCredit = $transactions->where('transaction_type', 'Credit')->sum('amount');
        $totalDebit = $transactions->where('transaction_type', 'Debit')->sum('amount');

        return [
            'statement_details' => [
                'account_no' => $accountNo,
                'period' => [
                    'from' => $fromDate,
                    'to' => $toDate
                ],
                'summary' => [
                    'total_credit' => number_format($totalCredit, 2),
                    'total_debit' => number_format($totalDebit, 2),
                    'net_movement' => number_format($totalCredit - $totalDebit, 2),
                    'transaction_count' => $transactions->count()
                ]
            ],
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'account_no' => $transaction->account_no,
                    'amount' => number_format($transaction->amount, 2),
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_date' => $transaction->transaction_date,
                    'description' => $transaction->description,
                    'running_balance' => number_format($transaction->current_balance, 2)
                ];
            })->toArray()
        ];
    }




    
}
