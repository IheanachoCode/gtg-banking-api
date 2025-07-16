<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     @OA\Property(property="request_id", type="string", example="ORD123"),
 *     @OA\Property(property="item_id", type="string", example="ITM123"),
 *     @OA\Property(property="item_name", type="string", example="Product Name"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="number", format="float", example=1000.00),
 *     @OA\Property(property="amount_paid", type="number", format="float", example=1500.00),
 *     @OA\Property(property="amount_expected", type="number", format="float", example=2000.00),
 *     @OA\Property(property="status", type="string", example="Pending"),
 *     @OA\Property(property="delivery", type="string", example="Processing")
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'request_id' => $this->request_id,
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'amount_paid' => $this->amount_paid,
            'amount_expected' => $this->amount_expected,
            'status' => $this->status,
            'delivery' => $this->delivery
        ];
    }
}