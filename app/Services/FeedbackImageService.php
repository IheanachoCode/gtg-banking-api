<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FeedbackImageService
{
    
    
    // public function uploadImages(array $data): array
    // {
    //     try {
    //         DB::beginTransaction();

    //         // Upload first image
    //         $firstImageName = $data['first_image']->getClientOriginalName();
    //         $firstUploaded = Storage::disk('public')->putFileAs(
    //             'FEEDBACKS',
    //             $data['first_image'],
    //             $firstImageName
    //         );

    //         // Upload second image
    //         $secondImageName = $data['second_image']->getClientOriginalName();
    //         $secondUploaded = Storage::disk('public')->putFileAs(
    //             'FEEDBACKS',
    //             $data['second_image'],
    //             $secondImageName
    //         );

    //         if (!$firstUploaded || !$secondUploaded) {
    //             throw new \Exception('Image upload failed');
    //         }

    //         // Create file records
    //         $fileData = [
    //             'file_source' => 'FEEDBACKS',
    //             'form' => 'Feedback',
    //             'user_id' => $data['reference_no'],
    //             'staff_id' => 'None',
    //             'date_created' => now()->format('Y-m-d')
    //         ];

    //         $firstFile = File::create(array_merge($fileData, ['url' => $firstImageName]));
    //         $secondFile = File::create(array_merge($fileData, ['url' => $secondImageName]));

    //         // Update feedback record
    //         $feedback = Feedback::where('reference_no', $data['reference_no'])
    //             ->update([
    //                 'first_image_url' => $firstImageName,
    //                 'second_image_url' => $secondImageName
    //             ]);

    //         DB::commit();

    //         return [
    //             'response' => true
    //         ];

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Feedback image upload failed', [
    //             'error' => $e->getMessage(),
    //             'reference_no' => $data['reference_no']
    //         ]);

    //         return [
    //             'response' => false
    //         ];
    //     }
    // }




    public function uploadImages(array $data): array
    {
        // TEMP: Bypass file move and DB logic for debugging
        // return [
        //     'status' => true,
        //     'message' => 'Test image upload response',
        //     'data' => [
        //         'first_image_url' => 'test1.jpg',
        //         'second_image_url' => 'test2.jpg'
        //     ]
        // ];
        
        try {
            DB::beginTransaction();

            $destinationPath = '/home/glorytog/public_html/spaceblade/public/FEEDBACKS/';

            // Upload first image
            $firstImageName = uniqid() . '_' . $data['first_image']->getClientOriginalName();
            $data['first_image']->move($destinationPath, $firstImageName);

            // Upload second image
            $secondImageName = uniqid() . '_' . $data['second_image']->getClientOriginalName();
            $data['second_image']->move($destinationPath, $secondImageName);

            // Create file records
            $fileData = [
                'file_source' => 'FEEDBACKS',
                'form' => 'Feedback',
                'user_id' => $data['reference_no'],
                'staff_id' => 'None',
                'date_created' => now()->format('Y-m-d')
            ];

            $firstFile = File::create(array_merge($fileData, ['url' => $firstImageName]));
            $secondFile = File::create(array_merge($fileData, ['url' => $secondImageName]));

            // Update feedback record
            $feedback = Feedback::where('reference_no', $data['reference_no'])
                ->update([
                    'first_image_url' => $firstImageName,
                    'second_image_url' => $secondImageName
                ]);

            DB::commit();

            return [
                'status' => true,
                'message' => 'Feedback image uploaded successfully.',
                'data' => [
                    'first_image_url' => $firstImageName,
                    'second_image_url' => $secondImageName
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Feedback image upload failed', [
                'error' => $e->getMessage(),
                'reference_no' => $data['reference_no']
            ]);

            return [
                'status' => false,
                'message' => 'Failed to upload feedback image.',
                'data' => null
            ];
        }
        
    }




}