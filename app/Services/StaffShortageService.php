<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffShortageService
{
    public function getStaffShortageTotal(string $staffId): float
    {
        $total = DB::table('shortage')
            ->where('accountOfficer', $staffId)
            ->sum('amount');

        return $total ?: 0.00;
    }
}
