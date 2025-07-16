<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffDepositService
{
    public function getDailyDepositTotal(string $staffId, ?string $date = null)
    {
        $date = $date ?? Carbon::today()->format('Y-m-d');

        $total = DB::table('client_deposit_withdrawal')
            ->where([
                'staff_id' => $staffId,
                'transaction_type' => 'Credit'
            ])
            ->whereDate('transaction_date', $date)
            ->sum('amount');

        return [
            'total_amount' => number_format($total, 2),
            'date' => $date,
            'has_transactions' => $total > 0
        ];
    }
}
