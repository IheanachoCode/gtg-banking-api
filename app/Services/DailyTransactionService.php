<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyTransactionService
{
    public function getTodaysTransactions(string $date): array
    {
        return DB::table('client_deposit_withdrawal')
            ->whereDate('transaction_date', $date)
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
}