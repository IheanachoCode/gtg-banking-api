<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PinValidationService
{
    public function validatePin(string $userId, string $pin): bool
    {
        return DB::table('client_registrations')
            ->where('user_id', $userId)
            ->where('Pin', $pin)
            ->exists();
    }
}