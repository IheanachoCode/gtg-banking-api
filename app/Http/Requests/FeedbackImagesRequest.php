<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackImagesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reference_no' => 'required|string',
            'first_image' => 'required|image',
            'second_image' => 'required|image'
        ];
    }
}