<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TransferResource",
 *     type="object",
 *     @OA\Property(property="account_no", type="string", example="1234567890"),
 *     @OA\Property(property="amount", type="number", format="float", example=1000.00),
 *     @OA\Property(property="transaction_type", type="string", example="Credit"),
 *     @OA\Property(property="transaction_id", type="string", example="TXN123"),
 *     @OA\Property(property="transaction_date", type="string", format="date", example="2025-06-11")
 * )
 */
class TransferResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account_no' => $this->account_no,
            'amount' => $this->amount,
            'transaction_type' => $this->transaction_type,
            'transaction_id' => $this->transaction_id,
            'transaction_date' => $this->transaction_date
        ];
    }
}