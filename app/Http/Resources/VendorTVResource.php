<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="VendorTVResource",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="DSTV")
 * )
 */
class VendorTVResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name
        ];
    }
}