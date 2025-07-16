<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => trim($this->lastname . ' ' . $this->othernames),
            'email' => $this->email,
            'phone' => $this->phone,
            'account_type' => $this->account_type,
            'verification_status' => $this->verification_status,
            'account_status' => $this->account_status,
            'created_at' => $this->Regdate ?? $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login' => $this->last_login_at,
            'profile_complete' => $this->isProfileComplete()
        ];
    }

    /**
     * Check if user profile is complete
     *
     * @return bool
     */
    protected function isProfileComplete(): bool
    {
        $requiredFields = [
            'lastname',
            'othernames',
            'phone',
            'email',
            'residential_address',
            'BVN'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }
}
