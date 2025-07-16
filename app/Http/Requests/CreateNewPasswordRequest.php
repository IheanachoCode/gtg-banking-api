<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewPasswordRequest extends FormRequest
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
            'email' => 'required_if:contact_type,email|nullable|email',
            'new_password' => 'required|string|min:6',
            'retype_password' => 'required|same:new_password'
        ];
    }
}