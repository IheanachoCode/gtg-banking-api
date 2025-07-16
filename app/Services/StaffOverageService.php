<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffOverageService
{
    public function getStaffOverageTotal(string $staffId): float
    {
        $total = DB::table('overage')
            ->where('accountOfficer', $staffId)
            ->sum('amount');

        return $total ?: 0.00;
    }
}
