<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string|exists:client_registrations,phone',
            'otp_code' => 'required|string|min:4|max:6'
        ];
    }
}