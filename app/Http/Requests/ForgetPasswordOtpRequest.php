<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contact_type' => 'required|in:phone,email',
            'phone' => 'required_if:contact_type,phone|nullable|string',
            'email' => 'required_if:contact_type,email|nullable|email'
        ];
    }
}