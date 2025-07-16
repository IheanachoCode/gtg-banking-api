<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupPhoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string|exists:client_registrations,phone'
        ];
    }
}