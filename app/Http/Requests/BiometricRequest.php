<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BiometricRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|string|exists:client_registrations,user_id'
        ];
    }
}
