<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     @OA\Property(property="item", type="string", example="Product Name"),
 *     @OA\Property(property="item_code", type="string", example="PRD001"),
 *     @OA\Property(property="purchased_price", type="number", format="float", example=1000.00),
 *     @OA\Property(property="selling_price", type="number", format="float", example=1500.00)
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'item' => $this->item,
            'item_code' => $this->item_code,
            'purchased_price' => $this->purchased_price,
            'selling_price' => $this->selling_price
        ];
    }
}