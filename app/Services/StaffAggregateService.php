<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffAggregateService
{
    protected $overageService;
    protected $shortageService;

    public function __construct(
        StaffOverageService $overageService,
        StaffShortageService $shortageService
    ) {
        $this->overageService = $overageService;
        $this->shortageService = $shortageService;
    }

    public function getStaffAggregateBalance(string $staffId): float
    {
        $overage = $this->overageService->getStaffOverageTotal($staffId);
        $shortage = $this->shortageService->getStaffShortageTotal($staffId);

        return $overage - $shortage;
    }

    public function getStaffAggregate(string $staffId): array
    {
        // Total deposits (credits)
        $totalDeposits = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->where('transaction_type', 'Credit')
            ->sum('amount');

        // Total withdrawals (debits)
        $totalWithdrawals = DB::table('client_deposit_withdrawal')
            ->where('staff_id', $staffId)
            ->where('transaction_type', 'Debit')
            ->sum('amount');

        // Overage (example: positive balance)
        $overage = $totalDeposits - $totalWithdrawals;

        // Shortage (example: negative balance, or you can define your own logic)
        $shortage = $overage < 0 ? abs($overage) : 0;

        return [
            'total_deposits' => number_format($totalDeposits, 2),
            'total_withdrawals' => number_format($totalWithdrawals, 2),
            'overage' => number_format($overage > 0 ? $overage : 0, 2),
            'shortage' => number_format($shortage, 2),
            'net_balance' => number_format($totalDeposits - $totalWithdrawals, 2),
        ];
    }
}
