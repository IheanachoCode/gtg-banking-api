<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'address' => $this->address,
            'business_sector' => $this->business_sector,
            'tax_identification_no' => $this->tax_identification_no,
            'insurance_id' => $this->insurance_id,
            'option' => $this->option,
            'option_price' => $this->formatPrice($this->option_price),
            'insured_benefits' => $this->insured_benefits,
            'status' => $this->status,
            'insurance_type' => $this->insurance_type,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format price for display
     *
     * @param mixed $price
     * @return string|null
     */
    private function formatPrice($price): ?string
    {
        if ($price === null) {
            return null;
        }

        return number_format((float) $price, 2);
    }
}

// Collection Resource
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InsuranceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'insurance_records' => $this->collection,
        ];
    }
}
