<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentUpdateService
{
    public function updatePaymentStatus(string $refNumber): bool
    {
        try {
            return DB::table('client_deposit_withdrawal')
                ->where('ref_no', $refNumber)
                ->update(['verify_stat' => 'Verified']) > 0;
        } catch (\Exception $e) {
            Log::error('Payment update error', [
                'error' => $e->getMessage(),
                'ref_no' => $refNumber
            ]);
            return false;
        }
    }
}