<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PassportUploadService
{
    public function uploadPassport(array $data): array
    {
        try {
            $user = User::where('phone', $data['phone'])->first();
            
            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User not found.',
                    'data' => null
                ];
            }

            $imageName = $data['passport']->getClientOriginalName();
            $uploaded = Storage::disk('public')->putFileAs(
                'PASSPORT',
                $data['passport'],
                $imageName
            );

            if (!$uploaded) {
                return [
                    'status' => false,
                    'message' => 'Failed to upload passport.',
                    'data' => null
                ];
            }

            $fileCreated = File::create([
                'file_source' => 'PASSPORT',
                'form' => 'Register',
                'url' => $imageName,
                'user_id' => $user->user_id,
                'staff_id' => 'None',
                'date_created' => now()->format('Y-m-d')
            ]);

            return [
                'status' => (bool) $fileCreated,
                'message' => $fileCreated ? 'Passport uploaded successfully.' : 'Failed to create file record.',
                'data' => $fileCreated ? $fileCreated->toArray() : null
            ];

        } catch (\Exception $e) {
            Log::error('Passport upload failed', [
                'error' => $e->getMessage(),
                'phone' => $data['phone']
            ]);

            return [
                'status' => false,
                'message' => 'Failed to upload passport.',
                'data' => null
            ];
        }
    }
}