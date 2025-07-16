<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DescoResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="descos", type="string", example="ENUGU"),
 *     @OA\Property(property="name", type="string", example="ENUGU DISTRIBUTION COMPANY"),
 *     @OA\Property(property="code", type="string", example="enugu"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class DescoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}