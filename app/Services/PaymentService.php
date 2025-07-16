<?php

namespace App\Services;

use App\Models\PaymentMode;
use Illuminate\Support\Collection;

class PaymentService
{
    public function getAllPaymentModes(): Collection
    {
        return PaymentMode::select('pay_mode')
            ->get()
            ->map(function ($mode) {
                return ['pay_mode' => $mode->pay_mode];
            });
    }
}
