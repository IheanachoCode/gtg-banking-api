<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AccountResource",
 *     type="object",
 *     @OA\Property(property="account_no", type="string", example="1234567890"),
 *     @OA\Property(property="account_name", type="string", example="John Doe"),
 *     @OA\Property(property="account_type", type="string", example="savings"),
 *     @OA\Property(property="account_status", type="string", example="active")
 * )
 */
class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account_no' => $this->account_no,
            'account_name' => $this->getAccountHolderName(),
            'account_type' => $this->account_type,
            'account_status' => $this->account_status
        ];
    }

    private function getAccountHolderName()
    {
        return trim(sprintf(
            '%s %s %s',
            $this->clientRegistration->surname,
            $this->clientRegistration->firstname,
            $this->clientRegistration->othername
        ));
    }
}