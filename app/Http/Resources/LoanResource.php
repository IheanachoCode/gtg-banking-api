<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LoanResource",
 *     type="object",
 *     @OA\Property(property="account_number", type="string", example="1234567890"),
 *     @OA\Property(property="date_issued", type="string", format="date", example="2025-01-01"),
 *     @OA\Property(property="client_name", type="string", example="John Doe"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="loan_type", type="string", example="Personal"),
 *     @OA\Property(property="processing_fee", type="number", format="float", example=1000.00),
 *     @OA\Property(property="amount", type="number", format="float", example=50000.00),
 *     @OA\Property(property="refence_no", type="string", example="LOAN123")
 * )
 */
class LoanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account_number' => $this->account_number,
            'date_issued' => $this->date_issued,
            'client_name' => $this->client_name,
            'address' => $this->address,
            'loan_type' => $this->loan_type,
            'processing_fee' => $this->processing_fee,
            'amount' => $this->amount,
            'refence_no' => $this->refence_no
        ];
    }
}