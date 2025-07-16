<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserTransactionService
{
    public function getRecentTransactions(string $accountNo, int $limit = 100): array
    {
        return DB::table('client_deposit_withdrawal')
            ->where('account_no', $accountNo)
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'description',
                'transaction_id',
                'created_at as transaction_date'
            ])
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'account_no' => $transaction->account_no,
                    'amount' => number_format($transaction->amount, 2),
                    'transaction_type' => $transaction->transaction_type,
                    'description' => $transaction->description,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_date' => $transaction->transaction_date
                ];
            })
            ->toArray();
    }


    protected function getBaseQuery()
    {
        return DB::table('client_deposit_withdrawal')
            ->select([
                'account_no',
                'amount',
                'transaction_type',
                'description',
                'transaction_id',
                'created_at'
            ]);
    }

   


public function getTransactionsByDateRange(string $accountNo, string $fromDate, string $toDate): array
{
    return DB::table('client_deposit_withdrawal')
        ->where('account_no', $accountNo)
        ->whereBetween('transaction_date', [$fromDate, $toDate])
        ->select([
            'account_no',
            'amount',
            'transaction_type',
            'transaction_id',
            'created_at'
        ])
        ->orderBy('created_at', 'DESC')
        ->get()
        ->map(function ($transaction) {
            return [
                'account_no' => $transaction->account_no,
                'amount' => number_format($transaction->amount, 2),
                'transaction_type' => $transaction->transaction_type,
                'transaction_id' => $transaction->transaction_id,
                'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s')
            ];
        })
        ->toArray();
}






public function getTransactionDates(string $accountNo): array
{
    return DB::table('client_deposit_withdrawal')
        ->where('account_no', $accountNo)
        ->selectRaw('DATE(created_at) as transaction_date')
        ->distinct()
        ->orderBy('transaction_date', 'DESC')
        ->pluck('transaction_date')
        ->toArray();
}



public function getLimitedTransactionDates(string $accountNo, int $limit = 10): array
{
    return DB::table('client_deposit_withdrawal')
        ->where('account_no', $accountNo)
        ->selectRaw('DATE(created_at) as transaction_date')
        ->distinct()
        ->orderBy('transaction_date', 'DESC')
        ->limit($limit)
        ->pluck('transaction_date')
        ->toArray();
}






}
