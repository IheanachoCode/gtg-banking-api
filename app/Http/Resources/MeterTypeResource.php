<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MeterTypeResource",
 *     type="object",
 *     @OA\Property(property="type", type="string", example="Prepaid")
 * )
 */
class MeterTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => $this->type
        ];
    }
}